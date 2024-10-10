<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\AccessDataRequest;
use App\Repositories\AccessDataRepository;
use App\Traits\UsesDynamicDatabaseConnection;

class AccessDataController extends Controller
{
    use UsesDynamicDatabaseConnection;
    
    protected $accessDataRepository;

    public function __construct(AccessDataRepository $accessDataRepository)
    {
        $this->accessDataRepository = $accessDataRepository;
    }

    public function index()
    {
       // dd(Auth::user());
        $accessData = $this->accessDataRepository->findByType('controller');
//\dd($accessData);
        if (request()->wantsJson()) {
            return response()->json($accessData);
        }

        return Inertia::render('configuracao/ControladorasConfig', [
            'accessData' => $accessData,
        ]);
    }


    // Lista apenas controladoras
    public function indexControllers()
    {
        $controllers = $this->accessDataRepository->findByType('controller');
       
        if (request()->wantsJson()) {
            return response()->json($controllers);
        }

        return Inertia::render('configuracao/ControllerList', [
            'controladora' => $controllers,
        ]);
    }
    public function show($id)
    {
        $accessData = $this->accessDataRepository->find($id);

        if (request()->wantsJson()) {
            return response()->json($accessData);
        }

        return Inertia::render('configuracao/ShowAccessData', [
            'accessData' => $accessData,
        ]);
    }
    // Lista apenas bases de dados
    public function indexDatabases()
    {
        $databases = $this->accessDataRepository->findByType('database');

        if (request()->wantsJson()) {
            return response()->json($databases);
        }

        return Inertia::render('configuracao/DatabaseList', [
            'databases' => $databases,
        ]);
    }

    public function store(AccessDataRequest $request)
    {
      // dd($request->all());
        try {
           // $data = $request->validated();
            $accessData = $this->accessDataRepository->create($request->all());

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Dados de acesso criados com sucesso!',
                    'data' => $accessData
                ]);
            }

            if ($request->input('type') === 'database') {
                return redirect()->route('database.index')
                    ->with('success', 'Configuração de banco de dados criada com sucesso!');
            } elseif ($request->input('type') === 'controller') {
                return redirect()->route('controladora.index')
                    ->with('success', 'Configuração de controladora criada com sucesso!');
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao criar dados de acesso: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(AccessDataRequest $request, $id)
    {
        try {
            $data = $request->validated();
            $accessData = $this->accessDataRepository->update($id, $data);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Dados de acesso atualizados com sucesso!',
                    'data' => $accessData
                ]);
            }

            if ($request->input('type') === 'database') {
                return redirect()->route('database.index')
                    ->with('success', 'Configuração de banco de dados criada com sucesso!');
            } elseif ($request->input('type') === 'controller') {
                return redirect()->route('controladora.index')
                    ->with('success', 'Configuração de controladora criada com sucesso!');
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar dados de acesso: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $this->accessDataRepository->delete($id);

            if (request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Dados de acesso deletados com sucesso!'
                ]);
            }

            return redirect()->route('accessData.index')
                ->with('success', 'Dados de acesso deletados com sucesso!');
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao deletar dados de acesso: ' . $e->getMessage()
            ], 500);
        }
    }

    // RadiusController.php
public function updateRegionConnection(Request $request)
{
    
    $user = Auth::user();
    
    // Verifica se o usuário é administrador
    if ($user->isAdmin()) {
        $region = $request->input('regiao');        
        
        $this->setRadiusConnection($user, $region);
        
        // Armazena a nova região na sessão para futuras requisições
        session(['regiao' => $region]);
        return response()->json(['message' => 'Conexão alterada com sucesso.'], 200);
    }

    return response()->json(['error' => 'Acesso não autorizado.'], 403);
}

}
