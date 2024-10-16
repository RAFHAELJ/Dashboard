<?php

namespace App\Http\Controllers;

use auth;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Traits\HandlesFileUpload;
use App\Repositories\LogRepository;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\LoginCustomizationRequest;
use App\Repositories\LoginCustomizationRepository;

class LoginCustomizationController extends Controller
{
    use HandlesFileUpload;
    protected $repository;

    public function __construct(LoginCustomizationRepository $repository,LogRepository $logRepository)
    {
        $this->repository = $repository;
        $this->logRepository = $logRepository;
    }

   
    public function index(Request $request)    {
       
 
        $customizations = $this->repository->getAll();        
   
        if ($request->wantsJson()) {
  
            return response()->json([
                'customizations' => $customizations
            ]);
        }
    
       
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
        
       
        $data = $request->validated();
     
        if ($request->hasFile('imagem')) {
            // Faz o upload da imagem de topo
            $data['elements'][0]['image'] = $this->uploadFile($request->file('imagem'), 'imagens');
        }
       
        if ($request->hasFile('backgroundImage')) {
            // Faz o upload da imagem de fundo
            $data['background_image'] = $this->uploadFile($request->file('backgroundImage'), 'imagens');
        }
        
        // Cria a customização de login com os dados atualizados
        $this->repository->create($data);
        $this->logRepository->createLog(auth()->id(), 'Adcionado Nova Configuração de Login', $request->regiao);
    
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
       
        $data = $request->validated();
 
        if ($request->hasFile('elements[0].image')) {
           
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
        
        $this->repository->update($id, $data);
        $this->logRepository->createLog(auth()->id(), 'Editou Configuração de Login', $customization->regiao);
        return response()->json(['message' => 'Login customization updated successfully!']);
    }

    public function destroy($id)
    {
        $this->repository->delete($id);
        $this->logRepository->createLog(auth()->id(), 'Apagado Configuração de Login');
        return redirect()->route('login_customizations.index')
                         ->with('success', 'Login customization deleted successfully!');
    }
}
