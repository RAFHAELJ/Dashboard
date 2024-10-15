<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Requests\RadiusRequest;
use Illuminate\Support\Facades\Auth;
use App\Repositories\RadiusRepository;

class RadiusController extends Controller {
    protected $radiusRepository;

    public function __construct(RadiusRepository $radiusRepository) {
        $this->radiusRepository = $radiusRepository;
    }
    public function index()
    { 
        $radius = $this->radiusRepository->all();
    
        // Verifica se a requisição quer um JSON (via fetch, axios, etc.)
        if (request()->wantsJson()) {
            return response()->json($radius);
        }
    
        // Se não for uma requisição JSON, renderize a página normalmente com Inertia
        return Inertia::render('radius/RadiusListar', [
            'radius' => $radius
        ]);
    }
    public function lista(){

        return Inertia::render('radius/Radius');
    }



    public function store(RadiusRequest $request) {
        
        // Validações
     
      // dd($request->all());
        try {
            // Criação do rádio no repositório
            $radio = $this->radiusRepository->create($request->all());
           
            return redirect()->route('radius.index')
                ->with('success', 'Rádio criado com sucesso!');
        } catch (\Exception $e) {
            // Captura a exceção e redireciona com erro
            return redirect()->back()->withErrors(['error' => 'Erro ao criar rádio: ' . $e->getMessage()]);
        }
    }
    

    public function show($id) {
        $radio = $this->radiusRepository->find($id);
        return Inertia::render('radius/Show', [
            'radius' => $radio
        ]);
    }

    public function update(RadiusRequest $request, $id) {

     //dd($request->all());
    
        try {
            // Atualização do rádio no repositório
            $radio = $this->radiusRepository->update($id, $request->all());
    
            return redirect()->route('radius.index')
                ->with('success', 'Rádio atualizado com sucesso!');
        } catch (\Exception $e) {
            // Captura a exceção e redireciona com erro
            return redirect()->back()->withErrors(['error' => 'Erro ao atualizar rádio: ' . $e->getMessage()]);
        }
    }
    

    public function destroy($id) {
        $this->radiusRepository->delete($id);
        return redirect()->route('radius.index')
            ->with('success', 'Rádio deletado com sucesso!');
    }


}
