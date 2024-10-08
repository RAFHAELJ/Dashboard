<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;

// Usando a conexão 'radius'
//$radiusData = DB::connection('radius')->table('radios')->get();
class UserController extends Controller
{
    protected $userRepository;
    

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function index()
    {
        
        $users = $this->userRepository->all();
        return Inertia::render('usuarios/ListaUsuariosDashboard', [
            'users' => $users
        ]);
    }
    public function count()
    {
        
        $usersCount = $this->userRepository->count();
        return $usersCount;
        
    }
    
    public function new()
    {        
        
        return Inertia::render('usuarios/NovoUsuarioDashboard');
    }
    public function store(UserRequest $request)
    {
        try {
          
    
            $user = $this->userRepository->create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'regiao' => $request->regiao,
                'nivel' => $request->nivel,
                'selectedActions' => $request->selectedActions,
                'selectedPages' => $request->selectedPages
            ]);
    
            return redirect()->route('users.index')->with('success', 'Usuário criado com sucesso!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->validator->errors())->withInput();
        }
    }
    

    public function show($id)
    {
        $user = $this->userRepository->find($id);
        if ($user) {
            return Inertia::render('Users/Show', [
                'user' => $user
            ]);
        } else {
            return Inertia::render('Errors/404', [
                'message' => 'Usuário não encontrado'
            ]);
        }
    }

    public function update(UserRequest $request, User $id)
    {
        try {
          
    
            $userResp = $this->userRepository->update($request, $id);
            if ($userResp) {
                return redirect()->route('users.index')->with('success', 'Usuário atualizado com sucesso!');
            } else {
                return Inertia::render('Errors/404', [
                    'message' => 'Usuário não encontrado'
                ]);
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->validator->errors())->withInput();
        }
    }
    

    public function destroy($id)
    {
        $user = $this->userRepository->delete($id);
        if ($user) {
            return redirect()->route('users.index')->with('success', 'Usuário deletado com sucesso!');
        } else {
            return Inertia::render('Errors/404', [
                'message' => 'Usuário não encontrado'
            ]);
        }
    }
}
