<?php

// app/Http/Controllers/Hotspot/HotspotController.php

namespace App\Http\Controllers\Hotspot;


use Log;
use Inertia\Inertia;
use App\Models\Regiao;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\RegiaoRepository;
use App\Repositories\HotspotRepository;
use Illuminate\Support\Facades\Session;
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
            'Customization' => $this->loginCustomizationRepository->find($id)
            , 'regiao' => $regiao
        ]);
        
    }
  

    public function logon($regiao, $id, $campanha_id)
    {
        // Obtém a URL de redirecionamento armazenada na sessão
        $url = session('url');
    
        return Inertia::render('hotspot/Logado', [
            'campanha' => $this->campanhaRepository->find($campanha_id),
            'login' => $this->loginCustomizationRepository->find($id),
            'url' => $url, // Passa a URL para o frontend
        ]);
    }
    
    public function showLoginForm(Request $request, $region)
    {      

        
       $login =  $this->hotspotRepository->login($region)->first();
       //dd($login);
       return inertia('hotspot/Login', [
        'login' => $login ?? null, 
        'validation' => 'controller',
    ]);
    }

    public function register(Request $request, $region)
    {
       

        // Passar os dados para o repository junto com a região
        $result = $this->hotspotRepository->registerUser($request->all(), $region);

        if (!$result['success']) {
            return redirect()->back()
                ->withErrors(['message' => $result['error']]) // Passa a mensagem de erro
                ->withInput();
        }
    
        // Redirecionar para a rota de login via Inertia
        return Inertia::location(route('hotspot.login', ['region' => $region]));
    
    }

    public function authenticate(Request $request, $region)
    {
       // dd($request->all());
       
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);       
        //\dd($request->all());
        $result = $this->hotspotRepository->authenticateUser($request->all(), $region, 'database');    
        if (!$result['success']) {        
            return redirect()->back()
                ->withErrors(['message' => $result['error']])
                ->withInput();
        }
        
        // Redirecionar para a rota usando Inertia
        return redirect()->route('hotspot.logon', [
            'region' => $region,
            'id' => $request->customization_id, // ID do usuário autenticado
            'campanha_id' => $result['campanha_id'] ?? 'null',
        ])->with('url', $result['url']);
        
}
public function logout($region)
{
   // \dd (Session::get('macradio'));
    // Limpa a sessão do hotspot
    Session::forget('hotspot.session');

    // Redireciona para a página de login com uma mensagem de sucesso
    return Inertia::location(route('hotspot.login', ['region' => $region]));
   
}
public function logoutRadius($region)
{
   // \dd (Session::get('macradio'));
    // Limpa a sessão do hotspot
    Session::forget('hotspot.session');

    // Redireciona para a página de login com uma mensagem de sucesso
    return Inertia::location(route('hotspot.radius.login', ['region' => $region]));
   
}
public function showRadiusLoginForm(Request $request, $region)
{
    $login = $this->hotspotRepository->login($region)->first();
    return Inertia::render('hotspot/Login', [
        'login' => $login ?? null,
        'validation' => 'hotspot',
    ]);
}

// Autenticação pelo RADIUS
public function authenticateRadius(Request $request, $region)
{
    $request->validate([
        'username' => 'required|string',
        'password' => 'required|string',
    ]);

    $result = $this->hotspotRepository->authenticateUser($request->all(), $region, 'radius');

   
    if (!$result['success']) {
        Log::info('Erro de autenticação: ' . $result['error']);
        return redirect()->back()
            ->withErrors(['message' => $result['error']])
            ->withInput();
    }

    return redirect()->route('hotspot.logon', [
        'region' => $region,
        'id' => $request->customization_id,
        'campanha_id' => $result['campanha_id'] ?? 'null',
    ])->with('url', $result['url']);
}



}
