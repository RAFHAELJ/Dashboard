<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\Permission;
use App\Models\Page;

class UserPermissionRepository
{
    // Buscar todas as ações
    public function getActions()
    {
        return Permission::all();
    }

    // Buscar todas as páginas
    public function getPages()
    {
        return Page::orderBy('name', 'asc')->get();
    }

    // Atualizar permissões associadas a um usuário
    public function updateUserPermissions(User $user, array $data)
    {
        // Remover todas as permissões existentes do usuário na tabela pivot
        $user->permissions()->detach();
    
        $permissionIds = $data['selectedActions'];  
        $pageIds = $data['selectedPages'];          
    
        // Inserir as novas permissões com as páginas associadas
        foreach ($permissionIds as $permissionId) {
            foreach ($pageIds as $pageId) {
                // Adicionar o relacionamento entre usuário, permissão (ação) e página
                $user->permissions()->attach($permissionId, ['page_id' => $pageId]);
            }
        }
    }
    

    // Obter permissões associadas a um usuário
    public function getPermissionsByUser(User $user)
    {
        // Obter permissões associadas ao usuário com as páginas relacionadas
        $permissions = $user->permissions()->withPivot('page_id')->get();

        return [
            'actions' => $permissions->pluck('id')->unique(), 
            'pages' => $permissions->pluck('pivot.page_id')->unique()
        ];
    }

    public function getPermissionsBySelect(User $user)
    {
        // Obter permissões associadas ao usuário com as páginas relacionadas
        $permissions = $user->permissions()->withPivot('page_id')->get();
        
        // Mapeie para garantir que retorna tanto o id quanto o name das ações em formato de array        
        $actions = $permissions->unique('id')->map(function($permission) {
            return [
                'id' => $permission->id, 
                'name' => $permission->name, 
            ];
        })->values()->toArray(); 
        
        // Buscar as páginas associadas com seus respectivos 'id' e 'name'       
        $pages = Page::whereIn('id', $permissions->pluck('pivot.page_id')->unique())
                    ->get(['id', 'name'])
                    ->toArray(); 
        
        return [
            'actions' => $actions, 
            'pages' => $pages, 
        ];
    }
    
    
    

}

