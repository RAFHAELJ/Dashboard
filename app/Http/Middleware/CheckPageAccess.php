<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Gate;

class CheckPageAccess
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
        // Obtém o nome da rota, que será usado como o slug da página
        $pageSlug = explode('.', $request->route()->getName())[0];  

        // Busca o objeto da página com base no slug
        
        $page = Page::where('slug', $pageSlug)->first();
        
        if (!$page) {
            abort(404, 'Página não encontrada,,.');
        }

        // Verifica se o usuário tem permissão para acessar essa página e realizar a ação
        if (Gate::denies('access-page', [$page, $action])) {
            abort(403, 'Você não tem permissão para acessar esta página ou realizar esta ação.');
        }

        // Continua o fluxo normal se a verificação passar
        return $next($request);
    }
}
