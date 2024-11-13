<?php
namespace App\Repositories;

use Carbon\Carbon;
use App\Models\Faq;
use App\Models\Campanha;
use App\Models\RadCheck;
use App\Models\RadioDash;
use App\Helpers\CpfHelper;
use App\Models\AccessData;
use App\Models\MacHistory;
use App\Models\LoginCustomization;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Symfony\Component\Process\Process;

class HotspotRepository
{
    protected $regiaoRepository;

    public function __construct(RegiaoRepository $regiaoRepository)
    {
        $this->regiaoRepository = $regiaoRepository;
    }

    public function login($regiao)
    {
        $regiaoId = $this->regiaoRepository->getRegiaoId($regiao);         
            
        
        return LoginCustomization::where('region', $regiaoId)->get();
    }
   

    public function registerUser(array $data, $region)
    {
        $cpf = CpfHelper::clearNumbers($data['cpf']);
        $telefone = CpfHelper::clearNumbers($data['telefone']);

        if ($cpf && !CpfHelper::validaCPF($cpf)) {
            return ['success' => false, 'error' => 'CPF inválido'];
        }

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'error' => 'E-mail inválido'];
        }

        if (RadCheck::where('UserName', $data['email'])->exists()) {
            return ['success' => false, 'error' => 'Este e-mail já está registrado'];
        }

        if (RadCheck::where('UserName', $data['nome'])->exists()) {
            return ['success' => false, 'error' => 'Este usuário já está registrado'];
        }

        $radcheck = new RadCheck();
        $radcheck->fill([
            'UserName' => $data['email'],
            'nome' => $data['nome'],
            'Attribute' => 'Cleartext-Password',
            'op' => ':=',
            'sexo' => $data['sexo'],
            'Value' => $cpf,
            'senha' => $data['senha'],
            'cpf' => $cpf,
            'telefone' => $telefone,
            'email' => $data['email'],
            'data_criacao' => now(),
            'ultimo_acesso' => now(),
        ]);
        $radcheck->save();

        $pappassword = CpfHelper::papPass(Session::get('hotspot.session.challenge'), config('radius.uamsecret'), $data['senha']);
        return ['success' => true, 'region_id' => $region];
    }

    public function authenticateUser(array $data, $region, $authType = 'database')
    {
        $regiaoId = $this->regiaoRepository->getRegiaoId($region);
        Session::put('DatausuarioWifiLogado', $data);
        if ($authType === 'database') {
            return $this->authenticateWithDatabase($data, $regiaoId);
        } elseif ($authType === 'radius') {
            return $this->authenticateWithRadius($data, $regiaoId);
        }

        return ['success' => false, 'error' => 'Tipo de autenticação desconhecido'];
    }

    private function getSaveUserDatabase($data)
    {
        $user = RadCheck::where('UserName', $data['username'])->first();

        if (!$user) {
            return ['success' => false, 'error' => 'E-mail não cadastrado'];
        }

        if ($user->Value !== $data['password']) {
            return ['success' => false, 'error' => 'Senha incorreta'];
        }

        $user->ultimo_acesso = now();
        $user->save();

        return ['success' => true, 'user' => $user];
    }

    private function authenticateWithDatabase(array $data, $regiaoId)
    {
        $response = $this->getSaveUserDatabase($data);

        if (!$response['success']) {
            return [
                'success' => false,
                'error' => $response['error']
            ];
        }
        

        return $this->handlePostAuthentication($response['user'], $data, $regiaoId, 'database');
    }

    private function authenticateWithRadius(array $data, $regiaoId)
    {
        $response = $this->getSaveUserDatabase($data);

        if (!$response['success']) {
            return [
                'success' => false,
                'error' => $response['error']
            ];
        }

        $accessData = AccessData::where('regiao', $regiaoId)->where('type', 'radius')->first();

        if (!$accessData) {
            return ['success' => false, 'error' => 'Configuração de autenticação RADIUS não encontrada'];
        }

        $process = new Process([
            'python3',
            app_path('radius/radius_auth.py'),
            $data['username'],
            $data['password'],
            $accessData->senha,
            $accessData->ip
        ]);

        $process->run();

        if (!$process->isSuccessful()) {
            Log::error('Falha ao executar o script Python', [
                'error' => $process->getErrorOutput(),
                'output' => $process->getOutput(),
            ]);
            return ['success' => false, 'error' => 'Falha na execução do script de autenticação RADIUS'];
        }

        $output = $process->getOutput();
        $response = json_decode($output, true);

        if (json_last_error() !== JSON_ERROR_NONE || !$response) {
            return ['success' => false, 'error' => 'Resposta inválida do script de autenticação RADIUS'];
        }

        if (!$response['success']) {
            return ['success' => false, 'error' => $response['message']];
        }
       

        return $this->handlePostAuthentication(null, $data, $regiaoId, 'radius');
    }
    public function conectado($regiao){
        $data = Session::get('DatausuarioWifiLogado');
        $regiaoId = $this->regiaoRepository->getRegiaoId($regiao);   
       
        return $this->handlePostAuthentication(null, $data, $regiaoId, 'database');
    }

    private function handlePostAuthentication($user = null, $data, $regiaoId, $authType)
    {
        $macradio = Session::get('hotspot.session.macradio');
        if (!$macradio) {
            return ['success' => false, 'error' => 'Rede Hotspot não selecionada. Verifique e desative a internet móvel e tente novamente'];
        }

        $pappassword = CpfHelper::papPass(Session::get('hotspot.session.challenge'), config('radius.uamsecret'), $data['password']);
        $campanha_id = $this->getActiveCampaign($macradio, $regiaoId);
        $url = $this->generateRedirectUrl($data, $pappassword, $regiaoId, $authType);
        
        Session::put('UrlusuarioWifiLogado', $url);
       
        

        return ['success' => true, 'url' => $url, 'campanha_id' => $campanha_id, 'region_id' => $regiaoId];
    }

    private function getActiveCampaign($macRadio, $regiaoId)
    {
        $campanhas = Campanha::where('status', 'ativa')
            ->whereDate('fim', '>=', Carbon::today())
            ->where('regiao', $regiaoId)
            ->get();

        foreach ($campanhas as $campanha) {
            $radios = json_decode($campanha->radios, true);
            $radiosCamp = RadioDash::whereIn('id', $radios)->get();

            foreach ($radiosCamp as $radio) {
                if ($radio->mac === $macRadio || MacHistory::where('radio_id', $radio->id)->where(function ($query) use ($macRadio) {
                    $query->where('mac_novo', $macRadio)
                          ->orWhere('mac_antigo', $macRadio);
                })->exists()) {
                    $today = now()->format('Y-m-d');
                    if ($campanha->comeco <= $today && $campanha->fim >= $today) {
                        return $campanha->id;
                    }
                }
            }
        }

        return null;
    }

    private function generateRedirectUrl($data, $pappassword, $regiaoId, $authType)
    {
       

        $sessionTimeout = 3600;
        // Capturar informações da sessão
        $uamip = Session::get('hotspot.session.uamip');
        $uamport = Session::get('hotspot.session.uamport');
        $ga_srvr = Session::get('hotspot.session.ga_srvr');
        $ga_ssid = Session::get('hotspot.session.ga_ssid');
        $ga_ap_mac = Session::get('hotspot.session.ga_ap_mac');
        $ga_nas_id = Session::get('hotspot.session.ga_nas_id');
        $ga_cmac = Session::get('hotspot.session.ga_cmac');
        $ga_Qv = Session::get('hotspot.session.ga_Qv');
        $emac = Session::get('hotspot.session.emac');
        $linklogin = Session::get('hotspot.session.linklogin');
    
        // Se o tipo de autenticação for 'database'
        if ($authType === 'database') {
            //ligowave
            if ($uamip) {
                return "http://$uamip:$uamport/logon?username=" . urlencode($data['username']) . "&password=" . urlencode($pappassword);
            }
            //cambium
            if ($ga_srvr) {
                return "http://$ga_srvr:880/cgi-bin/hotspot_login.cgi?ga_user=" . urlencode($data['username']) .
                       "&ga_pass=" . urlencode($data['password']) .
                       "&ga_ssid=" . urlencode($ga_ssid) .
                       "&ga_ap_mac=" . urlencode($ga_ap_mac) .
                       "&ga_nas_id=" . urlencode($ga_nas_id) .
                       "&ga_srvr=" . urlencode($ga_srvr) .
                       "&ga_cmac=" . urlencode($ga_cmac) .
                       "&ga_Qv=" . urlencode($ga_Qv);
            }
            //edge core
            if ($emac) {
                return "$linklogin/logon?username=" . urlencode($data['username']) . "&password=" . urlencode($data['password']) . "&emac=" . urlencode($emac);
            }
    
            return Session::get('hotspot.session.linklogin') . '?username=' . urlencode($data['username']) . '&password=' . urlencode($data['password']);
        }
    
        // Se o tipo de autenticação for 'radius'
        if ($authType === 'radius') {
            $redirectUrl = '';
    
            if ($ga_srvr) {
                $redirectUrl = "http://$ga_srvr:880/cgi-bin/hotspot_login.cgi";
            } elseif ($uamip) {
                $redirectUrl = "http://$uamip:$uamport/logon";
            }else if ($emac) {
                $redirectUrl = "$linklogin/logon?";

            }
    
            // Concatenar todos os atributos na URL
            $redirectUrl .= "?code=Access-Accept" .
                            "&Reply-Message=" . urlencode('Autenticação bem-sucedida') .
                            "&Session-Timeout=" . $sessionTimeout .
                            "&success=" . "true" .
                            "&User-Name=" . urlencode($data['username']) .
                            "&ga_user=" . urlencode($data['username']) .
                            "&ga_pass=" . urlencode($pappassword) .
                            "&ga_ssid=" . urlencode($ga_ssid) .
                            "&ga_ap_mac=" . urlencode($ga_ap_mac) .
                            "&ga_nas_id=" . urlencode($ga_nas_id) .
                            "&ga_srvr=" . urlencode($ga_srvr) .
                            "&ga_cmac=" . urlencode($ga_cmac) .
                            "&ga_Qv=" . urlencode($ga_Qv);
    
            return $redirectUrl;
        }
    
        // Retorno padrão se nenhuma condição for atendida
        return ['success' => false, 'message' => 'Tipo de autenticação desconhecido'];
    }

    public function showFaq( $region)
{;
    $regiaoId = $this->regiaoRepository->getRegiaoId($region);
   
      $faq =  Faq::where('regiao', $regiaoId)->get();
     
    return $faq;
}
    
    
}
