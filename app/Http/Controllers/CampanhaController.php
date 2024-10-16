<?php

namespace App\Http\Controllers;

use auth;
use Inertia\Inertia;
use App\Models\Campanha;
use Illuminate\Http\Request;
use App\Traits\HandlesFileUpload;
use App\Repositories\LogRepository;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\CampanhaRequest;
use Illuminate\Support\Facades\Storage;
use App\Repositories\CampanhaRepository;

class CampanhaController extends Controller
{
    use HandlesFileUpload;

    protected $campanhaRepository;

    public function __construct(CampanhaRepository $campanhaRepository,LogRepository $logRepository)
    {
        $this->campanhaRepository = $campanhaRepository;
        $this->logRepository = $logRepository;
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
        
        return Inertia::render('campanhas/CampanhasAdicionar');
    }


    public function store(CampanhaRequest $request)
    {
        $idade = $request->minimo . "," . $request->maxima;
        $radios = json_encode($request->radios);
        
        $videoPath = $request->hasFile('video') ? $this->uploadFile($request->file('video'), 'videos') : null;
        $capaPath = $request->hasFile('capa') ? $this->uploadFile($request->file('capa'), 'capas') : null;
        $imagemPath = $request->hasFile('imagem') ? $this->uploadFile($request->file('imagem'), 'imagens') : null;
        
        $campanha = $this->campanhaRepository->create([
            'nome' => $request->nome,
            'comeco' => $request->comeco,
            'fim' => $request->fim,
            'publico' => $request->publico,
            'radios' => $radios,
            'idade' => $idade,
            'tipo' => $request->tipo,
            'duracao' => $request->duracao,
            'video' => $videoPath,
            'capa' => $capaPath,
            'imagem' => $imagemPath,
            'tempo' => $request->duracao,
            'url' => $request->url,
            'regiao' => $request->regiao
        ]);
        $this->logRepository->createLog(auth()->id(), "Adicionado Campanha {$request->nome} ", $request->regiao);


        return redirect()->route('campanhas.index')->with('success', 'Campanha criada com sucesso!');
    }

    public function update(CampanhaRequest $request, $id)
    {
        $campanha = $this->campanhaRepository->find($id);
        $radios = json_encode($request->radios);
        
        if ($request->hasFile('video')) {
            if ($campanha->video) {
                Storage::delete($campanha->video);
            }
            $videoPath = $this->uploadFile($request->file('video'), 'videos');
        } else {
            $videoPath = $campanha->video;
        }

        if ($request->hasFile('capa')) {
            if ($campanha->capa) {
                Storage::delete($campanha->capa);
            }
            $capaPath = $this->uploadFile($request->file('capa'), 'capas');
        } else {
            $capaPath = $campanha->capa;
        }

        if ($request->hasFile('imagem')) {
            if ($campanha->imagem) {
                Storage::delete($campanha->imagem);
            }
            $imagemPath = $this->uploadFile($request->file('imagem'), 'imagens');
        } else {
            $imagemPath = $campanha->imagem;
        }

        $this->campanhaRepository->update([
            'nome' => $request->nome,
            'comeco' => $request->comeco,
            'fim' => $request->fim,
            'publico' => $request->publico,
            'radios' => $radios,
            'idade' => $request->idade,
            'tipo' => $request->tipo,
            'duracao' => $request->duracao,
            'video' => $videoPath,
            'capa' => $capaPath,
            'imagem' => $imagemPath,
            'tempo' => $request->duracao,
            'url' => $request->url,
            'regiao' => $request->regiao
        ], $campanha);

        $this->logRepository->createLog(auth()->id(), "Atualização Campanha {$request->nome} ", $request->regiao, $request->nome);

        return redirect()->route('campanhas.index')->with('success', 'Campanha atualizada com sucesso!');
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

 

    public function destroy($id)
    {
        
        $deleted = $this->campanhaRepository->delete($id);
        
        $this->logRepository->createLog(auth()->id(), "Apagado Campanha {$request->nome} ",null,$id);
        if ($deleted) {
            return redirect()->route('campanhas.index')->with('success', 'Campanha deletada com sucesso!');
        } else {
            
            return Inertia::render('Error', [
                'error' => 'Hotspot nao encontrado'
            ]);
        }
    }
}
