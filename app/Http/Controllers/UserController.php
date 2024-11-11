<?php

namespace App\Http\Controllers;

use auth;
use App\Models\User;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;
use App\Repositories\LogRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;

// Usando a conexão 'radius'
//$radiusData = DB::connection('radius')->table('radios')->get();
class UserController extends Controller
{
    protected $userRepository;
    

    public function __construct(UserRepository $userRepository,LogRepository $logRepository)
    {
        $this->userRepository = $userRepository;
        $this->logRepository = $logRepository;
    }

    public function index(Request $request)
    {
        
        $users = $this->userRepository->all($request,$request->input('per_page'));
        return Inertia::render('usuarios/ListaUsuariosDashboard', [
            'users' => $users
        ]);
    }
    public function count()
    {
        // Defina um tempo de expiração para o cache (em minutos)
        $cacheTime = 10;
    
        // Cache para a contagem de usuários por região
        $usersByRegion = Cache::remember('users_by_region', $cacheTime, function () {
            return $this->userRepository->countUsersByRegion();
        });
    
        // Cache para a contagem de usuários por nível
        $usersByLevel = Cache::remember('users_by_level', $cacheTime, function () {
            return $this->userRepository->countUsersByLevel();
        });
    
        // Formatar os dados para retorno
        $formattedRegionData = $usersByRegion->map(function ($item) {
            return [
                'name' => optional($item->regioes)->cidade ?? 'Desconhecido', 
                'value' => $item->user_count
            ];
        });
    
        $formattedLevelData = $usersByLevel->map(function ($item) {
            return [
                'name' => $item->nivel,
                'value' => $item->user_count
            ];
        });
    
        return response()->json([
            'success' => true,
            'data' => [
                'users_by_region' => $formattedRegionData,
                'users_by_level' => $formattedLevelData
            ]
        ]);
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
            
            $this->logRepository->createLog(auth()->id(), "Adcionado Novo Usuário {$request->name} ", $request->regiao);
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
            $this->logRepository->createLog(auth()->id(), "Atualizado Usuário {$request->name} ", $request->regiao);
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

    public function showChangePasswordForm()
    {
        return inertia('Auth/ChangePassword');
    }
    public function getPassword(User $user)
    {
        return $this->userRepository->getPassword($user);
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'password' => 'required|string|min:6|confirmed',
        ]);

        $userResp = $this->userRepository->updatePassword($request);
        $this->logRepository->createLog(auth()->id(), 'Atualizado Senha', $request->regiao);

        

        return redirect()->route('dashboard')->with('status', 'Senha atualizada com sucesso!');
    }
    

    public function destroy($id)
    {
        $user = $this->userRepository->delete($id);
        $this->logRepository->createLog(auth()->id(), "Apagado Usuário {$id} ");
        if ($user) {
            return redirect()->route('users.index')->with('success', 'Usuário deletado com sucesso!');
        } else {
            return Inertia::render('Errors/404', [
                'message' => 'Usuário não encontrado'
            ]);
        }
    }


}
