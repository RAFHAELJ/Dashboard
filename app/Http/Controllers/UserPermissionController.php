<?php

namespace App\Http\Controllers;


use auth;
use App\Models\User;
use Illuminate\Http\Request;
use App\Repositories\LogRepository;
use App\Repositories\UserPermissionRepository;

class UserPermissionController extends Controller
{
    protected $userPermissionRepository;

    public function __construct(UserPermissionRepository $userPermissionRepository,LogRepository $logRepository)
    {
        $this->userPermissionRepository = $userPermissionRepository;
        $this->logRepository = $logRepository;

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
        $this->logRepository->createLog(auth()->id(), "Atualizado Permissões de Usuário {$user->name} ", $request->regiao);
        return response()->json(['message' => 'Permissões atualizadas com sucesso!']);
    }


}
