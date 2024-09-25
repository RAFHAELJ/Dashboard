<?php

// app/Repositories/UserRepository.php
namespace App\Repositories;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\UserPermissionService;
use App\Repositories\UserPermissionRepository;


class UserRepository{

    public function __construct(UserPermissionRepository $userPermissionRepository,UserPermissionService $userPermissionService)
    {
        $this->userPermissionRepository = $userPermissionRepository;
        $this->userPermissionService = $userPermissionService;
    }

    public function all() {
        $users = User::with('regiao')->paginate();       
        return $users;
        
    }
    public function count() {
        return User::all()->count();
    }

    public function find($id) {

       

        return User::find($id);
    }

    public function create(array $data)
    {
        
        $user = User::create($data);

        // Associar permissões (ações e páginas) se forem fornecidas
        if ((isset($data['selectedActions'])&& !empty($data['selectedActions'])) && (isset($data['selectedPages']) && !empty($data['selectedPages']))) {
            $this->userPermissionRepository->updateUserPermissions($user, $data);
        }else{
            $this->userPermissionService->assignDefaultPermissions($user);
        }

        return $user;
    }

    public function update(Request $request, User $user)
    {
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'regiao' => $request->regiao,
            'nivel' => $request->nivel
        ]);

        // Atualizar permissões (ações e páginas)
        if ($request->has('selectedActions') && $request->has('selectedPages')) {
            $this->userPermissionRepository->updateUserPermissions($user, $request->all());
        }

        return $user;
    }

    public function delete($id) {
        return User::destroy($id);
    }
}
