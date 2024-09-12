<?php

namespace App\Http\Controllers;

use App\Models\Regiao;
use App\Repositories\RegiaoRepository;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RegiaoController extends Controller
{
    protected $regiaoRepository;

    public function __construct(RegiaoRepository $regiaoRepository)
    {
        $this->regiaoRepository = $regiaoRepository;
    }

    // Index (exibição)
    public function index()
    {
        $regioes = $this->regiaoRepository->all();
    
        
        if (request()->wantsJson()) {
            
            return response()->json($regioes);
        }    
        // Se não for uma requisição JSON, renderize a página normalmente com Inertia
        return Inertia::render('Regioes/Index', [
            'regioes' => $regioes
        ]);
    }
    

    // Criar nova região
    public function store(Request $request)
    {
        $request->validate([
            'cidade' => 'required|string|max:255',
            'bairros' => 'required|string|max:255',
        ]);

        $regiao = $this->regiaoRepository->create($request->all());

        if ($request->expectsJson()) {
            return response()->json($regiao, 201);
        }

        return redirect()->route('regioes.index');
    }

    // Editar uma região
    public function update(Request $request, $id)
    {
        $request->validate([
            'cidade' => 'required|string|max:255',
            'bairros' => 'required',
        ]);

        $regiao = $this->regiaoRepository->update($id, $request->all());

        if ($request->expectsJson()) {
            return response()->json($regiao, 200);
        }

        return redirect()->route('regioes.index');
    }

    // Excluir uma região
    public function destroy($id)
    {
        $this->regiaoRepository->delete($id);

        if (request()->expectsJson()) {
            return response()->json(null, 204);
        }

        return redirect()->route('regioes.index');
    }
}

