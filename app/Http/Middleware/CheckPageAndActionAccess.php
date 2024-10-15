<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use App\Models\Page;

class CheckPageAndActionAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $action
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $action)
    {
        // Obtém o slug da página a partir do nome da rota
        $pageSlug = $request->route()->getName();

        // Busca o objeto Page com base no slug
        $page = Page::where('slug', $pageSlug)->first();

        // Verifica se a página existe
        if (!$page) {
            abort(404, 'Página não encontrada.');
        }

        // Verifica se o usuário tem permissão para acessar essa página e realizar a ação
        if (Gate::denies('access-page', [$page, $action])) {
            // Se o usuário não tiver permissão, retorna erro 403 (Acesso negado)
            abort(403, 'Você não tem permissão para acessar esta página ou realizar esta ação.');
        }

        // Se o usuário tiver permissão, continua o fluxo normal da requisição
        return $next($request);
    }
}
