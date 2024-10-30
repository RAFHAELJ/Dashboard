<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Regiao;
use App\Traits\UsesDynamicDatabaseConnection;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DynamicDatabaseConnectionHotSpot
{
    use UsesDynamicDatabaseConnection;

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
        // Extrair e normalizar a região da URL
        $region = $this->extractRegionFromUrl($request->url());

        if ($region) {
            // Buscar a região na tabela 'regioes'
            $regionRecord = Regiao::whereRaw('LOWER(REPLACE(cidade, " ", "")) = ?', [strtolower(str_replace(' ', '', $region))])
                ->first();

            if ($regionRecord) {
                // Define a conexão dinâmica com base na região encontrada
                $connected = $this->setRadiusConnection(Auth::user(), $regionRecord->id, 'hotspot');

                if (!$connected) {
                    // Retorna uma resposta de erro amigável usando Inertia
                    return abort(403, 'Houve um problema ao acessar o banco de dados. Por favor, tente novamente mais tarde ou entre em contato com o suporte.');
                }

                // Define a região atual na sessão
                session(['regiao' => $regionRecord->id]);
            } else {
                // Região não encontrada
                return abort(404);
            }
        }

        return $next($request);
    }

    /**
     * Extrair a região da URL.
     *
     * @param  string  $url
     * @return string|null
     */
    private function extractRegionFromUrl($url)
    {
        // Extrai o segundo segmento da URL (exemplo: /hotspot/campolargo/login)
        $segments = explode('/', parse_url($url, PHP_URL_PATH));

        // Supõe que a região é o terceiro segmento, ajuste conforme necessário
        return $segments[2] ?? null;
    }
}
