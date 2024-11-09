<?php

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
    protected $loginCustomizationRepository;
    protected $campanhaRepository;

    public function __construct(
        HotspotRepository $hotspotRepository,
        LoginCustomizationRepository $loginCustomizationRepository,
        CampanhaRepository $campanhaRepository
    ) {
        $this->hotspotRepository = $hotspotRepository;
        $this->loginCustomizationRepository = $loginCustomizationRepository;
        $this->campanhaRepository = $campanhaRepository;
    }

    public function new($regiao, $id)
    {
        return Inertia::render('hotspot/Cadastro', [
            'Customization' => $this->loginCustomizationRepository->find($id),
            'regiao' => $regiao,
        ]);
    }

    public function logon($regiao, $id, $campanha_id)
    {
        $url = session('url'); // URL armazenada na sessão para redirecionamento

        return Inertia::render('hotspot/Logado', [
            'campanha' => $this->campanhaRepository->find($campanha_id),
            'login' => $this->loginCustomizationRepository->find($id),
            'url' => $url,
        ]);
    }

    public function showLoginForm(Request $request, $region)
    {
        $login = $this->hotspotRepository->login($region)->first();
        return Inertia::render('hotspot/Login', [
            'login' => $login ?? null,
            'validation' => 'controller',
        ]);
    }

    public function register(Request $request, $region)
    {
        $result = $this->hotspotRepository->registerUser($request->all(), $region);

        if (!$result['success']) {
            return redirect()->back()
                ->withErrors(['message' => $result['error']])
                ->withInput();
        }

        return Inertia::location(route('hotspot.login', ['region' => $region]));
    }

    public function authenticate(Request $request, $region)
    {
        return $this->performAuthentication($request, $region, 'database');
    }

    public function authenticateRadius(Request $request, $region)
    {
        return $this->performAuthentication($request, $region, 'radius');
    }

    private function performAuthentication(Request $request, $region, $method)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $result = $this->hotspotRepository->authenticateUser($request->all(), $region, $method);

        if (!$result['success']) {
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

    public function logout($region)
    {
        return $this->performLogout($region, 'hotspot.login');
    }

    public function logoutRadius($region)
    {
        return $this->performLogout($region, 'hotspot.radius.login');
    }

    private function performLogout($region, $routeName)
    {
        Session::forget('hotspot.session');
        return Inertia::location(route($routeName, ['region' => $region]));
    }

    public function showRadiusLoginForm(Request $request, $region)
    {
        $login = $this->hotspotRepository->login($region)->first();
        return Inertia::render('hotspot/Login', [
            'login' => $login ?? null,
            'validation' => 'hotspot',
        ]);
    }

    public function showFaq(Request $request, $region, $id)
    {
        $faq = $this->hotspotRepository->showFaq($region)->first();
        return Inertia::render('hotspot/Faq', [
            'faq' => $faq ?? null,
            'custom' => $this->loginCustomizationRepository->find($id),
        ]);
    }
}
