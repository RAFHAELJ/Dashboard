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
                        'dashboard', 'home', 'campanhas.index', 'campanhas.show'
                    ],
                    'permissions' => ['ler', 'gravar', 'atualizar']
                ];

            case 'Supervisor':
                return [
                    'pages' => [
                        'dashboard', 'home', 'campanhas.index', 'regioes.index'
                    ],
                    'permissions' => ['ler', 'alterar', 'gravar']
                ];

            case 'Marketing':
                return [
                    'pages' => [
                        'estatisticas.index', 'estatisticas.show', 'edicao.index'
                    ],
                    'permissions' => ['ler', 'alterar']
                ];

            default:
                return [
                    'pages' => [],
                    'permissions' => []
                ];
        }
    }
}
