<?php

// app/Repositories/UserRepository.php
namespace App\Repositories;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use App\Services\UserPermissionService;
use App\Repositories\UserPermissionRepository;


class UserRepository{

    public function __construct(UserPermissionRepository $userPermissionRepository,UserPermissionService $userPermissionService)
    {
        $this->userPermissionRepository = $userPermissionRepository;
        $this->userPermissionService = $userPermissionService;
    }

    public function all(Request $request,int $per_page) {

        if ($request->has('search')) {
            $search = $request->search;
            return User::where(function($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
            })->paginate($per_page);
    }
        $users = User::byRegiao()->with('regioes')->paginate($per_page);       
        return $users;
        
    }
    public function count() {
        return User::all()->count();
    }
    public function countUsersByRegion()
    {
        return  User::with('regiao')
        ->select('regiao', DB::raw('count(*) as user_count'))
        ->groupBy('regiao')
        ->get();
    }

    public function countUsersByLevel()
    {
        return User::select('nivel', DB::raw('count(*) as user_count'))
                    ->groupBy('nivel')
                    ->get();
    }
    public function find($id) {

       

        return User::byRegiao()->find($id);
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

    public function getPassword(User $user)
    {
        // Verifica se o usuário já tem uma senha definida
       
           
            // Define a senha padrão "wnidobrasil" caso esteja ausente
            $user->password = Hash::make('wnidobrasil');
            $user->save();
        
    
        return $user;
    }

    public function updatePassword(Request $request)
    {      

        $user = Auth::user();
        $user->password = Hash::make($request->password);
        $user->save();

        return $user;
    }

    public function delete($id) {
        return User::destroy($id);
    }
}
