<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\LoginCustomizationRequest;
use App\Repositories\LoginCustomizationRepository;
use App\Traits\HandlesFileUpload;
class LoginCustomizationController extends Controller
{
    use HandlesFileUpload;
    protected $repository;

    public function __construct(LoginCustomizationRepository $repository)
    {
        $this->repository = $repository;
    }

    // Método para exibir a tela com Inertia.js
    public function index(Request $request)
    {
       
        // Buscar os dados da tabela de customizações
        $customizations = $this->repository->getAll();
        
        // Verificar se a solicitação quer JSON (via Axios ou API)
        if ($request->wantsJson()) {
            //Log::info($customizations);
            // Retornar uma resposta JSON
            return response()->json([
                'customizations' => $customizations
            ]);
        }
    
        // Retornar uma view usando Inertia.js se não for uma solicitação JSON
        return Inertia::render('loginLayout/ListaHotSpot', [
            'customizations' => $customizations
        ]);
    }
    

    public function create()
    {
        return Inertia::render('loginLayout/LoginCustomizations');
    }

    public function store(LoginCustomizationRequest $request)
    {
        // Valida os dados
       
        $data = $request->validated();
      // dd($data);
        // Verifica se há arquivos para upload
        if ($request->hasFile('imagem')) {
            // Faz o upload da imagem de topo
            $data['elements'][0]['image'] = $this->uploadFile($request->file('imagem'), 'imagens');
        }
       // dd($data);
        if ($request->hasFile('backgroundImage')) {
            // Faz o upload da imagem de fundo
            $data['background_image'] = $this->uploadFile($request->file('backgroundImage'), 'imagens');
        }
        
        // Cria a customização de login com os dados atualizados
        $this->repository->create($data);
    
        return response()->json(['message' => 'Login customization created successfully!']);
    }

    public function edit($id)
    {
        //dd($id);
        $customization = $this->repository->find($id);

        return Inertia::render('loginLayout/LoginCustomizations', [
            'customization' => $customization
        ]);
    }

    public function update(LoginCustomizationRequest $request, $id)
    {
        
        // Valida os dados
        //dd($request->all());
        $data = $request->validated();
    //dd()
        // Verifica se há arquivos para upload
        if ($request->hasFile('elements[0].image')) {
            // Faz o upload da nova imagem de topo
            $data['elements'][0]['image'] = $this->uploadFile($request->file('elements[0].image'), 'imagens');
        }
    
        if ($request->hasFile('backgroundImage')) {
            // Faz o upload da nova imagem de fundo
            $data['background_image'] = $this->uploadFile($request->file('backgroundImage'), 'imagens');
        }
    
         // Verifica se há arquivos para upload
         if ($request->hasFile('imagem')) {
            // Faz o upload da imagem de topo
            $data['image'] = $this->uploadFile($request->file('imagem'), 'imagens');
        }
       
        // Atualiza a customização de login
        $this->repository->update($id, $data);
    
        return response()->json(['message' => 'Login customization updated successfully!']);
    }

    public function destroy($id)
    {
        $this->repository->delete($id);

        return redirect()->route('login_customizations.index')
                         ->with('success', 'Login customization deleted successfully!');
    }
}
