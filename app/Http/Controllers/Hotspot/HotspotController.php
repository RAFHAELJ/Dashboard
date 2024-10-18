<?php

// app/Http/Controllers/Hotspot/HotspotController.php

namespace App\Http\Controllers\Hotspot;

use App\Models\Regiao;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\HotspotRepository;

class HotspotController extends Controller
{
    protected $hotspotRepository;

    public function __construct(HotspotRepository $hotspotRepository)
    {
        $this->hotspotRepository = $hotspotRepository;
    }

    public function showLoginForm($region)
    {
       // 
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

