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
        //dd($request->all());
        // Aqui o $request já estará validado
        $data = $request->validated();
        if ($request->hasFile('imagem')) {
            $imagemPath = $request->hasFile('imagem') ? $this->uploadFile($request->file('imagem'), 'imagens') : null;
        }else if($request->hasFile('backgroundImage')){
            $backgroundImage = $request->hasFile('backgroundImage') ? $this->uploadFile($request->file('backgroundImage'), 'imagens') : null;
        }
        

        // Criando a customização de login
        $this->repository->create($data);

        return response()->json(['message' => 'Login customization updated successfully!']);

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
