<?php

namespace App\Http\Controllers;

use App\Models\Campanha;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Repositories\CampanhaRepository;
use Illuminate\Support\Facades\Hash;
use App\Traits\HandlesFileUpload;
class CampanhaController extends Controller
{
    use HandlesFileUpload;

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
        
        return Inertia::render('campanhas/CampanhasAdicionar');
    }


    public function store(Request $request)
    {
        //dd($request->all());
         $request->validate([
            'nome' => 'required|string|max:255',
            'comeco' => 'required|date',
            'fim' => 'required|date|after_or_equal:comeco',
            'publico' => 'required|string',
            'minimo' => 'required|integer|min:0',
            'maxima' => 'required|integer|gte:minimo',
            'tipo' => 'required|string|in:video,imagem',
            'duracao' => 'required_if:tipo,video|integer|min:1',
                   
           
           
        ]);
        $idade =$request->minimo.",".$request->maxima;
        $radios = json_encode($request->radios);
        // Manipulando o upload dos arquivos
        $videoPath = $request->hasFile('video') ? $this->uploadFile($request->file('video'), 'videos') : null;
        $capaPath = $request->hasFile('capa') ? $this->uploadFile($request->file('capa'), 'capas') : null;
        $imagemPath = $request->hasFile('imagem') ? $this->uploadFile($request->file('imagem'), 'imagens') : null;
        // Criação da campanha
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
            'tempo' => $request->tempo,
            'url' => $request->url,
            'regiao' => $request->regiao
            
        ]);
    
        return redirect()->route('campanhas.index')->with('success', 'Campanha criada com sucesso!');
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

    public function update(Request $request, $id)
    {
      
        // Validação dos dados
        $request->validate([
            'nome' => 'required|string|max:255',
            'comeco' => 'required|date',
            'fim' => 'required|date|after_or_equal:comeco',
            'publico' => 'required|string',
            'idade' => 'required',            
            'tipo' => 'required|string|in:video,imagem',
            'duracao' => 'required_if:tipo,video|integer|min:1',
        ]);
       // dd($request->all());
        // Encontrando a campanha pelo ID
        $campanha = $this->campanhaRepository->find($id);
    
        // Manipulando os dados de idade e rádios
        
        $radios = json_encode($request->radios);
    
        // Manipulando o upload dos arquivos (se existirem novos uploads)
        if ($request->hasFile('video')) {
            // Exclui o vídeo antigo, se houver
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
    
        // Atualizando os dados da campanha
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
            'tempo' => $request->tempo,
            'url' => $request->url,
            'regiao' => $request->regiao
        ], $campanha);
    
        return redirect()->route('campanhas.index')->with('success', 'Campanha atualizada com sucesso!');
    }
    

    public function destroy($id)
    {
        
        $deleted = $this->campanhaRepository->delete($id);
        
        
        if ($deleted) {
            return redirect()->route('campanhas.index')->with('success', 'Campanha deletada com sucesso!');
        } else {
            
            return Inertia::render('Errors/404', [
                'message' => 'Campanha não encontrada'
            ]);
        }
    }
}
