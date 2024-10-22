<?php

// app/Http/Controllers/Hotspot/HotspotController.php

namespace App\Http\Controllers\Hotspot;


use Inertia\Inertia;
use App\Models\Regiao;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\HotspotRepository;
use App\Repositories\CampanhaRepository;
use App\Repositories\LoginCustomizationRepository;

class HotspotController extends Controller
{
    protected $hotspotRepository;

    public function __construct(HotspotRepository $hotspotRepository, LoginCustomizationRepository $loginCustomizationRepository ,CampanhaRepository $campanhaRepository)
    {
        $this->hotspotRepository = $hotspotRepository;
        $this->loginCustomizationRepository = $loginCustomizationRepository;
        $this->campanhaRepository = $campanhaRepository;
    }
    public function new($regiao,$id)
    {
       // dd($this->loginCustomizationRepository->find($id));
      // dd($this->loginCustomizationRepository->find(1));
        return Inertia::render('hotspot/Cadastro',[
            'Customization' => $this->loginCustomizationRepository->find($id)]);
        
    }
    public function logon($regiao,$id,$campanha_id)
    {
        return Inertia::render('hotspot/Logado',[    
            'campanha' => $this->campanhaRepository->find($campanha_id),'login' => $this->loginCustomizationRepository->find($id)]);
    }
    public function showLoginForm($region)
    {
       
       $regiaoId = Regiao::whereRaw('LOWER(REPLACE(cidade, " ", "")) = ?', [strtolower(str_replace(' ', '', $region))])
       ->pluck('id')
       ->first(); 

     
       $login =  $this->hotspotRepository->login($regiaoId)->first();
       //dd($login);
       return inertia('hotspot/Login', [
        'login' => $login ?? null, // Garante que 'login' seja enviado, mesmo que null
    ]);
    }

    public function register(Request $request, $region)
    {
        $request->validate([
            'username' => 'required|string',
            'cpf' => 'nullable|string',
            'email' => 'nullable|email',
            'telefone' => 'nullable|string',
            'password' => 'required|string|confirmed',
        ]);

        // Passar os dados para o repository junto com a região
        $result = $this->hotspotRepository->registerUser($request->all(), $region);

        if (!$result['success']) {
            return back()->withErrors([$result['error']]);
        }

        return redirect()->to($result['url'])->with('message', 'Cadastro concluído com sucesso');
    }

    public function authenticate(Request $request, $region)
    {
        dd($request->all(), $region);
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $result = $this->hotspotRepository->authenticateUser($request->all(), $region);

        if (!$result['success']) {
            return back()->withErrors([$result['error']]);
        }

        return redirect()->to($result['url'])->with('message', 'Autenticação realizada com sucesso');
    }
}

