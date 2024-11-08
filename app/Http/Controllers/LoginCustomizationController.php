<?php
namespace App\Http\Controllers;

use Auth;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Traits\HandlesFileUpload;
use App\Repositories\LogRepository;
use App\Http\Requests\LoginCustomizationRequest;
use App\Repositories\LoginCustomizationRepository;

class LoginCustomizationController extends Controller
{
    use HandlesFileUpload;

    protected $repository;
    protected $logRepository;

    public function __construct(LoginCustomizationRepository $repository, LogRepository $logRepository)
    {
        $this->repository = $repository;
        $this->logRepository = $logRepository;
    }

    public function index(Request $request)
    {
        $customizations = $this->repository->getAll();

        if ($request->wantsJson()) {
            return response()->json(['customizations' => $customizations]);
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

        // Consolidando o tratamento de uploads de arquivos
        $data = $this->handleUploads($request, $data, true);

        $customization = $this->repository->create($data);
        $this->logRepository->createLog(Auth::id(), 'Adicionada Nova Configuração de Login', $request->regiao);

        return response()->json(['message' => 'Login customization created successfully!', 'data' => $customization]);
    }

    public function edit($id)
    {
        $customization = $this->repository->find($id);

        return Inertia::render('loginLayout/LoginCustomizations', [
            'customization' => $customization
        ]);
    }

    public function update(LoginCustomizationRequest $request, $id)
    {
        $data = $request->validated();

        // Consolidando o tratamento de uploads de arquivos, para edição
        $data = $this->handleUploads($request, $data);

        $this->repository->update($id, $data);
        $this->logRepository->createLog(Auth::id(), 'Editou Configuração de Login');

        return response()->json(['message' => 'Login customization updated successfully!']);
    }

    public function destroy($id)
    {
        $customization = $this->repository->find($id);

        // Deleta as imagens associadas à customização antes de remover o registro
        $this->deleteAssociatedImages($customization);

        $this->repository->delete($id);
        $this->logRepository->createLog(Auth::id(), 'Apagou Configuração de Login');

        return response()->json(['message' => 'Login customization deleted successfully!']);
    }

    /**
     * Consolidate the file uploads handling
     */
    protected function handleUploads(Request $request, array $data, $isNew = false)
    {
        // Upload da imagem do topo, elemento indexado como `elements[0]`
        if ($request->hasFile('imagem')) {
            if (!$isNew && isset($data['elements'][0]['image'])) {
                $this->deleteFile($data['elements'][0]['image']);
            }
            $data['elements'][0]['image'] = $this->uploadFile($request->file('imagem'), 'imagens');
        }

        // Upload da imagem de fundo
        if ($request->hasFile('backgroundImage')) {
            if (!$isNew && isset($data['background_image'])) {
                $this->deleteFile($data['background_image']);
            }
            $data['background_image'] = $this->uploadFile($request->file('backgroundImage'), 'imagens');
        }

        return $data;
    }

    /**
     * Delete associated images when removing a customization
     */
    protected function deleteAssociatedImages($customization)
    {
        if (isset($customization->elements[0]['image'])) {
            $this->deleteFile($customization->elements[0]['image']);
        }
        if (isset($customization->background_image)) {
            $this->deleteFile($customization->background_image);
        }
    }
}
