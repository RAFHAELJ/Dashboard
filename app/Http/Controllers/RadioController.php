<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Repositories\RadioRepository;

class RadioController extends Controller {
    protected $radioRepository;

    public function __construct(RadioRepository $radioRepository) {
        $this->radioRepository = $radioRepository;
    }
    public function index()
    {
        $radios = $this->radioRepository->all();
    
        // Verifica se a requisição quer um JSON (via fetch, axios, etc.)
        if (request()->wantsJson()) {
            return response()->json($radios);
        }
    
        // Se não for uma requisição JSON, renderize a página normalmente com Inertia
        return Inertia::render('radios/RadiosListar', [
            'radios' => $radios
        ]);
    }

    public function radioRelatorio(Request $request)
    {
        
        $radios = $this->radioRepository->radioRelatorio($request);
    
        
        return Inertia::render('radios/RelatoriosRadios', [
            'radios' => $radios,
            'filters' => $request->all(), 
        ]);
    }

    public function store(Request $request) {
        
        // Validações
        $validatedData = $request->validate([
            'radio' => 'required|string|max:255',
            'mac' => 'required|string',
            'geo' => 'required|string|max:255',
            'endereco' => 'required|string|max:255',
            'info' => 'required|string|max:255',
            'regiao'=>'required',
        ]);
       
        try {
            // Criação do rádio no repositório
            $radio = $this->radioRepository->create($validatedData);
           
            return redirect()->route('radios.index')
                ->with('success', 'Rádio criado com sucesso!');
        } catch (\Exception $e) {
            // Captura a exceção e redireciona com erro
            return redirect()->back()->withErrors(['error' => 'Erro ao criar rádio: ' . $e->getMessage()]);
        }
    }
    

    public function show($id) {
        $radio = $this->radioRepository->find($id);
        return Inertia::render('radios/Show', [
            'radio' => $radio
        ]);
    }

    public function update(Request $request, $id) {

        // Validações
        $validatedData = $request->validate([
            'radio' => 'required|string|max:255',
            'mac' => 'required|string',
            'geo' => 'required|string|max:255',
            'endereco' => 'required|string|max:255',
            'info' => 'required|string|max:255',
            'regiao'=>'required',
        ]);
    
        try {
            // Atualização do rádio no repositório
            $radio = $this->radioRepository->update($id, $validatedData);
    
            return redirect()->route('radios.index')
                ->with('success', 'Rádio atualizado com sucesso!');
        } catch (\Exception $e) {
            // Captura a exceção e redireciona com erro
            return redirect()->back()->withErrors(['error' => 'Erro ao atualizar rádio: ' . $e->getMessage()]);
        }
    }
    

    public function destroy($id) {
        $this->radioRepository->delete($id);
        return redirect()->route('radios.index')
            ->with('success', 'Rádio deletado com sucesso!');
    }

    public function getGeoRadio()
    {
        $data = $this->radioRepository->getGeoRadio();

        return inertia('radios/GetMapaRadios', ['data' => $data]);
    }
    public function track(Request $request)
    {
        
        $startDate = $request->input('startD');
        $endDate = $request->input('endD');
        $username = $request->input('username');
       
        $usersFinal = $this->radioRepository->getTrackedUsers($startDate, $endDate ,$perPage = 10, $username);

       
        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Data',
                'data' => $usersFinal,
            ]);
        }

        
        return Inertia::render('radios/RastrearRadios', [
            'users' => $usersFinal
        ]);
    }
}
