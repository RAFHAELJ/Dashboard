<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Repositories\UsuarioRepository;

class UsuarioController extends Controller
{
    protected $userRepository;

    public function __construct(UsuarioRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function index()
    {
        
        $usuarios = $this->userRepository->all();
        
        return Inertia::render('usuarios/ListaUsuariosRadio', [
            'usuarios' => $usuarios
        ]);
    }
    public function new()
    {        
        
        return Inertia::render('usuarios/NovoUsuariosRadio');
    }
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:usuarios',
            'password' => 'required|min:6',
        ]);

        $user = $this->userRepository->create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('usuarios.index')->with('success', 'Usuário criado com sucesso!');
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

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|string|email|max:255|unique:usuarios,email,' ,
            
        ]);        

        $userResp = $this->userRepository->update($request, $user);
        if ($userResp) {
            return redirect()->route('usuarios.index')->with('success', 'Usuário atualizado com sucesso!');
        } else {
            return Inertia::render('Errors/404', [
                'message' => 'Usuário não encontrado'
            ]);
        }
    }

    public function destroy($id)
    {
        $user = $this->userRepository->delete($id);
        if ($user) {
            return redirect()->route('usuarios.index')->with('success', 'Usuário deletado com sucesso!');
        } else {
            return Inertia::render('Errors/404', [
                'message' => 'Usuário não encontrado'
            ]);
        }
    }
}
