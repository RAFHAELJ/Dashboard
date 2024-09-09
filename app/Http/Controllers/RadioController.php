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
        $radio = $this->radioRepository->create($request->all());
        return redirect()->route('radios.index')
            ->with('success', 'Rádio criado com sucesso!');
    }

    public function show($id) {
        $radio = $this->radioRepository->find($id);
        return Inertia::render('radios/Show', [
            'radio' => $radio
        ]);
    }

    public function update(Request $request, $id) {
        $radio = $this->radioRepository->update($id, $request->all());
        return redirect()->route('radios.index')
            ->with('success', 'Rádio atualizado com sucesso!');
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
}
