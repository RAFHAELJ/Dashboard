<?php
namespace App\Http\Middleware;

use Log;
use Closure;
use Illuminate\Support\Facades\Session;

class StoreHotspotSessionMiddleware
{
    public function handle($request, Closure $next)
    {
                // Se já existem erros na sessão, não altere
                if ($request->session()->has('errors')) {
                    return $next($request);
                }
        // Capturar parâmetros da URL e armazenar na sessão específica do hotspot
        $params = [
            'challenge', 'uamip', 'uamport', 'userurl',
            'ga_ssid', 'ga_ap_mac', 'ga_nas_id',
            'ga_srvr', 'ga_cmac', 'ga_Qv', 'link-login-only',
            'loginurl', 'identity', 'emac', 'called'
        ];

        $hotspotSession = [];

        foreach ($params as $param) {
            if ($request->has($param)) {
                $hotspotSession[$param] = $request->get($param);
            }
        }
       // Log::info('Dados da sessão Hotspot:', $hotspotSession);
        // Lógica para definir 'linklogin' caso 'uamip' não esteja presente
        if (!$request->has('uamip') && ($request->has('link-login-only') || $request->has('loginurl'))) {
            $hotspotSession['linklogin'] = $request->get('link-login-only', $request->get('loginurl'));
        }

        // Lógica para definir 'macradio'
        if (!$request->has('called')) {
            $macradio = $request->get('identity') ?? $request->get('emac') ?? $request->get('ga_ap_mac');
            if ($macradio) {
                $hotspotSession['macradio'] = $macradio;
            }
        } else {
            $hotspotSession['macradio'] = $request->get('called');
        }

        // Armazene as variáveis específicas na sessão do hotspot
        if (!empty($hotspotSession)) {
            Session::put('hotspot.session', $hotspotSession);
            Session::save(); 
        }

       // Log::info('Sessão atual do Hotspot:', Session::get('hotspot.session'));


        return $next($request);
    }
}
