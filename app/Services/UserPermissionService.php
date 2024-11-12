<?php

namespace App\Services;

use App\Models\Page;
use App\Models\Permission;
use App\Models\User;

class UserPermissionService
{
    /**
     * Define permissões padrão para usuários de acordo com o nível.
     */
    public function assignDefaultPermissions(User $user)
    {
        $defaultPermissions = $this->getDefaultPermissions($user->nivel);

        // Atribuir as permissões padrão ao usuário
        foreach ($defaultPermissions['pages'] as $slug) {
            $page = Page::where('slug', $slug)->first();
            
            if ($page) {
                foreach ($defaultPermissions['permissions'] as $permissionName) {
                    $permission = Permission::where('name', $permissionName)->first();
                    if ($permission) {
                        $user->permissions()->attach($permission->id, ['page_id' => $page->id]);
                    }
                }
            }
        }
    }

    /**
     * Define quais páginas e permissões são padrão para cada nível.
     */
    private function getDefaultPermissions(string $nivel)
    {
        switch ($nivel) {
            case 'Operador':
                return [
                    'pages' => [
                        'dashboard', 'home', 'usuariosDash', 'usuarios','campanhas','controladora','incluirRadios','configurarRadio','radios'
                    ],
                    'permissions' => ['ler', 'gravar', 'atualizar']
                ];

            case 'Supervisor':
                return [
                    'pages' => [
                        'dashboard', 'home', 'radios', 'mapaRadios','rastrearRadio','rastrearUsoRadio','usuarios','usuariosDash','configurarRadio','users','campanhas','login_customizations','controladora','faq','incluirRadios'
                    ],
                    'permissions' => ['ler', 'atualizar', 'gravar', 'excluir']
                ];

            case 'Marketing':
                return [
                    'pages' => [
                        'home', 'login_customizations', 'campanhas','dashboard'
                    ],
                    'permissions' => ['ler', 'atualizar','gravar','excluir']
                ];

            default:
                return [
                    'pages' => [],
                    'permissions' => []
                ];
        }
    }
}
