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
        return Inertia::render('configuracao/Index', [
            'regioes' => $regioes
        ]);
    }
    

    // Criar nova região
    public function store(Request $request)
    {
        // Validações
        $validatedData = $request->validate([
            'cidade' => 'required|string|max:255',
            'bairros' => 'required|string|max:255',
        ]);
    
        try {
            // Criação da região
            $regiao = $this->regiaoRepository->create($validatedData);
    
            if ($request->expectsJson()) {
                return response()->json($regiao, 201); // Retorna como JSON caso seja esperado
            }
    
            return redirect()->route('regioes.index')
                ->with('success', 'Região criada com sucesso!');
        } catch (\Exception $e) {
            // Captura a exceção e redireciona com erros
            return Inertia::render('Error', [
                'error' => 'Erro ao criar a região: ' . $e->getMessage()
            ]);
        }
    }
    

    // Editar uma região
    public function update(Request $request, $id)
    {
        // Validações
        $validatedData = $request->validate([
            'cidade' => 'required|string|max:255',
            'bairros' => 'required|string|max:255',
        ]);
    
        try {
            // Atualização da região
            $regiao = $this->regiaoRepository->update($id, $validatedData);
    
            if ($request->expectsJson()) {
                return response()->json($regiao, 200); // Retorna como JSON caso seja esperado
            }
    
            return redirect()->route('regioes.index')
                ->with('success', 'Região atualizada com sucesso!');
        } catch (\Exception $e) {
            // Captura a exceção e redireciona com erros
            return Inertia::render('Error', [
                'error' => 'erro ao atualizar a região: ' . $e->getMessage()
            ]);
        }
    }
    

    // Excluir uma região
    public function destroy($id)
    {
        try {
            // Tenta deletar a região
            $this->regiaoRepository->delete($id);
    
            if (request()->expectsJson()) {
                return response()->json(null, 204); // Retorna status 204 (No Content) em caso de sucesso
            }
    
            return redirect()->route('regioes.index')
                ->with('success', 'Região deletada com sucesso!');
        } catch (\Exception $e) {
            // Captura a exceção e retorna uma mensagem de erro apropriada
            if (request()->expectsJson()) {
                return Inertia::render('Error', [
                    'error' => 'Erro ao deletar a região: ' . $e->getMessage()
                ]);
            }
    
            return redirect()->back()->withErrors(['error' => 'Erro ao deletar a região: ' . $e->getMessage()]);
        }
    }
    
}

