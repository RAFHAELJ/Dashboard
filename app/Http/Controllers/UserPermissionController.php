<?php

namespace App\Http\Controllers;


use App\Repositories\UserPermissionRepository;
use App\Models\User;
use Illuminate\Http\Request;

class UserPermissionController extends Controller
{
    protected $userPermissionRepository;

    public function __construct(UserPermissionRepository $userPermissionRepository)
    {
        $this->userPermissionRepository = $userPermissionRepository;
    }

    public function getActions()
    {
        $actions = $this->userPermissionRepository->getActions();
        return response()->json([
            'actions' => $actions->map(function ($action) {
                return [
                    'id' => $action->id,   
                    'name' => $action->name
                ];
            })
        ]);
    }   
       
// Buscar todas as páginas
public function getPages()
{
    $pages = $this->userPermissionRepository->getPages();
    return response()->json([
        'pages' => $pages->map(function ($page) {
            return [
                'id' => $page->id,   
                'name' => $page->name 
            ];
        })
    ]);
}


    // Carregar permissões ao editar um usuário
    public function getPermissions(User $user)
    {
        return $this->userPermissionRepository->getPermissionsByUser($user);
    }

    public function getPermissionsSelect(User $user)
    {
        return $this->userPermissionRepository->getPermissionsBySelect($user);
    }

    // Atualizar permissões do usuário
    public function updatePermissions(Request $request, User $user)
    {
        $validated = $request->validated();
        $this->userPermissionRepository->updateUserPermissions($user, $validated);
        return response()->json(['message' => 'Permissões atualizadas com sucesso!']);
    }


}
