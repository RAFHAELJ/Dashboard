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
       

        $user = $this->userRepository->create([
            'nome' => $request->name,
            'email' => $request->email,
            'value' => Hash::make($request->password),
            'telefone' => $request->telefone
        ]);

        return redirect()->route('usuarios.index')->with('success', 'Usuário criado com sucesso!');
    }

 

    public function update(Request $request,  $id)
    {
       
                

        $userResp = $this->userRepository->update($request->all(), $id);
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
