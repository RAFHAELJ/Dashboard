<?php
namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Requests\FormsRequest;
use App\Http\Controllers\Controller;
use App\Repositories\FormRepository;

class FormController extends Controller
{
    protected $formRepository;

    public function __construct(FormRepository $formRepository)
    {
        $this->formRepository = $formRepository;
    }

    // Listar formulários
    public function index(Request $request)
    {
       // \dd($request->all());
        $forms = $this->formRepository->getAllForms();

        if ($request->wantsJson()) {
            return response()->json($forms);
        }    

        
        return Inertia::render('forms/ViewForm', [
            'forms' => $forms // Passa para o componente Vue
        ]);
    }

    // Criar formulário
    public function store(FormsRequest $request)
    {
      // \dd($request->all());
        $form = $this->formRepository->createForm($request->all());

        // Verifica se é uma chamada AJAX ou Inertia
        if ($request->wantsJson()) {
            return response()->json($form, 201);
        }

        return redirect()->route('forms.index')->with('success', 'Formulário criado com sucesso.');
    }

    // Mostrar formulário
    public function show($id, Request $request)
    {
        $form = $this->formRepository->getFormById($id);

        if ($request->wantsJson()) {
            return response()->json($form);
        }

        return inertia('Forms/Show', ['form' => $form]);
    }

    // Atualizar formulário
    public function update(FormsRequest $request, $id)
    {
        $form = $this->formRepository->updateForm($id, $request->validated());

        if ($request->wantsJson()) {
            return response()->json($form, 200);
        }

        return redirect()->route('forms.index')->with('success', 'Formulário atualizado com sucesso.');
    }

    // Deletar formulário
    public function destroy($id, Request $request)
    {
        $this->formRepository->deleteForm($id);

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Formulário deletado com sucesso.']);
        }

        return redirect()->route('forms.index')->with('success', 'Formulário deletado com sucesso.');
    }
}
