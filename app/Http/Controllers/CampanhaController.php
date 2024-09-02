<?php

namespace App\Http\Controllers;

use App\Models\Campanha;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Repositories\CampanhaRepository;
use Illuminate\Support\Facades\Hash;

class CampanhaController extends Controller
{
    protected $campanhaRepository;

    public function __construct(CampanhaRepository $campanhaRepository)
    {
        $this->campanhaRepository = $campanhaRepository;
    }

    public function index()
    {
        
        $campanhas = $this->campanhaRepository->all();
        return Inertia::render('campanhas/CampanhasListar', [
            'campanhas' => $campanhas
        ]);
    }
    public function new()
    {        
        
        return Inertia::render('campanhas/NovoUsuarioDashboard');
    }
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:campanhas',
            'password' => 'required|min:6',
        ]);

        $user = $this->campanhaRepository->create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('campanhas.index')->with('success', 'Campanha criado com sucesso!');
    }

    public function show($id)
    {
        $user = $this->campanhaRepository->find($id);
        if ($user) {
            return Inertia::render('Campanhas/Show', [
                'user' => $user
            ]);
        } else {
            return Inertia::render('Errors/404', [
                'message' => 'Campanha não encontrado'
            ]);
        }
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|string|email|max:255|unique:campanhas,email,' ,
            
        ]);        

        $userResp = $this->campanhaRepository->update($request, $user);
        if ($userResp) {
            return redirect()->route('campanhas.index')->with('success', 'Campanha atualizado com sucesso!');
        } else {
            return Inertia::render('Errors/404', [
                'message' => 'Campanha não encontrado'
            ]);
        }
    }

    public function destroy($id)
    {
        $user = $this->campanhaRepository->delete($id);
        if ($user) {
            return redirect()->route('campanhas.index')->with('success', 'Campanha deletado com sucesso!');
        } else {
            return Inertia::render('Errors/404', [
                'message' => 'Campanha não encontrado'
            ]);
        }
    }
}
