<?php

namespace App\Http\Controllers;

use auth;
use Inertia\Inertia;
use App\Models\Regiao;
use Illuminate\Http\Request;
use App\Repositories\LogRepository;
use App\Repositories\RegiaoRepository;

class RegiaoController extends Controller
{
    protected $regiaoRepository;

    public function __construct(RegiaoRepository $regiaoRepository,LogRepository $logRepository)
    {
        $this->regiaoRepository = $regiaoRepository;
        $this->logRepository = $logRepository;
    }

    public function index(Request $request)
    {
        $regioes = $this->regiaoRepository->all($request,$request->input('per_page'));
    
        
        if (request()->wantsJson()) {
            
            return response()->json($regioes);
        }    
       
        return Inertia::render('configuracao/Index', [
            'regioes' => $regioes
        ]);
    }
    

   
    public function store(Request $request)
    {
  
        $validatedData = $request->validate([
            'cidade' => 'required|string|max:255',
            'bairros' => 'required|string|max:255',
        ]);
    
        try {
            // Criação da região
            $regiao = $this->regiaoRepository->create($validatedData);
            $this->logRepository->createLog(auth()->id(), 'Adcionado Nova Regiao', $request->regiao);
    
            if ($request->expectsJson()) {
                return response()->json($regiao, 201); 
            }
    
            return redirect()->route('regioes.index')
                ->with('success', 'Região criada com sucesso!');
        } catch (\Exception $e) {
           
            return Inertia::render('Error', [
                'error' => 'Erro ao criar a região: ' . $e->getMessage()
            ]);
        }
    }   

    
    public function update(Request $request, $id)
    {
      
        $validatedData = $request->validate([
            'cidade' => 'required|string|max:255',
            'bairros' => 'required|string|max:255',
        ]);
    
        try {
            
            $regiao = $this->regiaoRepository->update($id, $validatedData);
            $this->logRepository->createLog(auth()->id(), 'Atualização de Regiao', $request->regiao);
    
            if ($request->expectsJson()) {
                return response()->json($regiao, 200); 
            }
    
            return redirect()->route('regioes.index')
                ->with('success', 'Região atualizada com sucesso!');
        } catch (\Exception $e) {
           
            return Inertia::render('Error', [
                'error' => 'erro ao atualizar a região: ' . $e->getMessage()
            ]);
        }
    } 

  
    public function destroy($id)
    {
        try {
          
            $this->regiaoRepository->delete($id);
            $this->logRepository->createLog(auth()->id(), 'Deletado Regiao');
    
            if (request()->expectsJson()) {
                return response()->json(null, 204); 
            }
    
            return redirect()->route('regioes.index')
                ->with('success', 'Região deletada com sucesso!');
        } catch (\Exception $e) {
            
            if (request()->expectsJson()) {
                return Inertia::render('Error', [
                    'error' => 'Erro ao deletar a região: ' . $e->getMessage()
                ]);
            }
    
            return redirect()->back()->withErrors(['error' => 'Erro ao deletar a região: ' . $e->getMessage()]);
        }
    }
    
}

