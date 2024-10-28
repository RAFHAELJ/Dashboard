<?php
namespace App\Repositories;

use Carbon\Carbon;
use App\Models\Radio;
use App\Models\Campanha;
use App\Models\RadCheck;
use App\Models\RadioDash;
use App\Helpers\CpfHelper;
use App\Models\MacHistory;
use App\Models\LoginCustomization;
use Illuminate\Support\Facades\DB;
use App\Repositories\RegiaoRepository;
use Illuminate\Support\Facades\Session;

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
        // Limpar e validar o CPF
        $cpf = CpfHelper::clearNumbers($data['cpf']);
        $telefone = CpfHelper::clearNumbers($data['telefone']);

        if ($cpf && !CpfHelper::validaCPF($cpf)) {
            return ['success' => false, 'error' => 'CPF inválido'];
        }

        // Verificar se o e-mail é válido
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'error' => 'E-mail inválido'];
        }

        // Verificar se o e-mail já está registrado
        $existingEmail = RadCheck::where('UserName', $data['email'])->first();
        if ($existingEmail) {
            return ['success' => false, 'error' => 'Este e-mail já está registrado'];
        }

        // Verificar se o nome de usuário já está registrado
        $existingUser = RadCheck::where('UserName', $data['nome'])->first();
        if ($existingUser) {
            return ['success' => false, 'error' => 'Este usuário já está registrado'];
        }

        // Criar um novo registro no RadCheck
        $radcheck = new RadCheck();
        $radcheck->UserName = $data['email'];
        $radcheck->nome = $data['nome'];
        $radcheck->Attribute = 'Cleartext-Password';
        $radcheck->op = ':=';
        $radcheck->sexo = $data['sexo'];
        $radcheck->Value = $data['cpf'];
        $radcheck->senha = $data['senha'];
        $radcheck->cpf = $cpf;
        $radcheck->telefone = $telefone;
        $radcheck->email = $data['email'];
        $radcheck->data_criacao = now();
        $radcheck->ultimo_acesso = now();
        $radcheck->save();

        // Gerar a senha PAP (opcional)
        $pappassword = CpfHelper::papPass(Session::get('hotspot.session.challenge'), config('radius.uamsecret'), $data['senha']);

        return ['success' => true, 'region_id' => $region];
    }

    public function authenticateUser(array $data, $region)
    {
        $regiaoId = $this->regiaoRepository->getRegiaoId($region);
        $user = RadCheck::where('UserName', $data['username'])->first();

        if (!$user) {
            return ['success' => false, 'error' => 'E-mail não cadastrado'];
        }

        // Verificar se a senha corresponde ao e-mail
        if ($user->Value !== $data['password']) {
            return ['success' => false, 'error' => 'Senha incorreta'];
        }

        $user->ultimo_acesso = now();
        $user->save();

        // Recuperar o valor de 'macradio' da sessão do hotspot
        $macradio = Session::get('hotspot.session.macradio');
        if (!$macradio) {
            return ['success' => false, 'error' => 'Conexao de rede não está ativa no hotspot,Verifica se o 3g esta desligado e se o hotspot esta ativo'];
        }

        $pappassword = CpfHelper::papPass(Session::get('hotspot.session.challenge'), config('radius.uamsecret'), $data['password']);
        $campanha_id = $this->getActiveCampaign($macradio, $regiaoId);
        $url = $this->generateRedirectUrl($user, $pappassword, $regiaoId);

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
                if ($radio->mac === $macRadio) {
                    $today = now()->format('Y-m-d');
                    if ($campanha->comeco <= $today && $campanha->fim >= $today) {
                        return $campanha->id;
                    }
                }

                $macHistory = MacHistory::where('radio_id', $radio->id)
                    ->where(function ($query) use ($macRadio) {
                        $query->where('mac_novo', $macRadio)
                              ->orWhere('mac_antigo', $macRadio);
                    })
                    ->first();

                if ($macHistory) {
                    $today = now()->format('Y-m-d');
                    if ($campanha->comeco <= $today && $campanha->fim >= $today) {
                        return $campanha->id;
                    }
                }
            }
        }

        return null;
    }

    private function generateRedirectUrl($user, $pappassword, $regiaoId)
    {
        $uamip = Session::get('hotspot.session.uamip');
        $uamport = Session::get('hotspot.session.uamport');
        $ga_srvr = Session::get('hotspot.session.ga_srvr');
        $ga_ssid = Session::get('hotspot.session.ga_ssid');
        $ga_ap_mac = Session::get('hotspot.session.ga_ap_mac');
        $ga_nas_id = Session::get('hotspot.session.ga_nas_id');
        $ga_cmac = Session::get('hotspot.session.ga_cmac');
        $ga_Qv = Session::get('hotspot.session.ga_Qv');
        $emac = Session::get('hotspot.session.emac');

        if ($uamip) {
            return "http://$uamip:$uamport/logon?username=" . urlencode($user->UserName) . "&password=" . urlencode($pappassword);
        }

        if ($ga_srvr) {
            return "http://$ga_srvr:880/cgi-bin/hotspot_login.cgi?ga_user=" . urlencode($user->UserName) .
                   "&ga_pass=" . urlencode($user->Value) .
                   "&ga_ssid=" . urlencode($ga_ssid) .
                   "&ga_ap_mac=" . urlencode($ga_ap_mac) .
                   "&ga_nas_id=" . urlencode($ga_nas_id) .
                   "&ga_srvr=" . urlencode($ga_srvr) .
                   "&ga_cmac=" . urlencode($ga_cmac) .
                   "&ga_Qv=" . urlencode($ga_Qv);
        }

        if ($emac) {
            return "http://$uamip:$uamport/logon?username=" . urlencode($user->UserName) . "&password=" . urlencode($pappassword) . "&emac=" . urlencode($emac);
        }

        return Session::get('hotspot.session.linklogin') . '?username=' . urlencode($user->UserName) . '&password=' . urlencode($user->Value);
    }
}
