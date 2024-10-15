<?php

namespace App\Providers;

use App\Models\Page;
use App\Models\User;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
    
        // Definindo o Gate para verificar se o usuário pode acessar a página e realizar a ação
        Gate::define('access-page', function (User $user, Page $page, $action) {
            // Administrador tem acesso a tudo
            if ($user->nivel === 'Administrador') {
                return true;
            }
    
            // Supervisores têm acesso apenas à sua região e ações permitidas
            if ($user->nivel === 'Supervisor' && $user->regiao === $page->regiao) {
                return $user->hasPermissionForPage($page, $action);
            }

            // Verifica se o usuário tem permissão para a página e ação específica
            return $user->hasPermissionForPage($page, $action);
        });

        // Compartilhar permissões globalmente com o Inertia
        Inertia::share([
            'permissions' => function () {
                if (auth()->check()) {
                    $user = auth()->user();

                    // Se o usuário é Administrador, retornar permissão total (pode ser um valor simbólico ou todas as ações possíveis)
                    if ($user->nivel === 'Administrador') {
                        // Administrador tem todas as permissões, aqui usamos uma chave especial
                        return ['all'];
                    }                    

                    // Para outros usuários, carregar as permissões específicas de página e ação
                    $permissions = $user->permissions()
                    ->with('pages') // Carrega as páginas associadas às permissões
                    ->get()
                    ->flatMap(function ($permission) {
                        // Mapeia as permissões com base nas páginas associadas
                        return $permission->pages->map(function ($page) use ($permission) {
                            return [
                                'page_slug' => $page->slug, // Acessa o slug da página
                                'action' => $permission->name, // Ação da permissão (ler, criar, etc.)
                            ];
                        });
                    })
                    ->groupBy('page_slug') // Agrupa pelo slug da página
                    ->map(function ($group) {
                        return $group->pluck('action')->unique()->toArray(); // Ações permitidas, sem duplicatas
                    });
                
               
                
                return $permissions->toArray(); // Retorna como array
                
                }

                return [];
            },
        ]);
    }
}
