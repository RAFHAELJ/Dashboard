<?php

namespace App\Http\Controllers;


use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Repositories\LoginCustomizationRepository;


class LoginCustomizationController extends Controller
{
    protected $repository;

    public function __construct(LoginCustomizationRepository $repository)
    {
        $this->repository = $repository;
    }

    // Método para exibir a tela com Inertia.js
    public function index()
    {
        $customizations = $this->repository->getAll();

        // Retornando com Inertia o componente Vue
        return Inertia::render('LoginCustomizations', [
            'customizations' => $customizations
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'top_image' => 'nullable|string',
            'background_type' => 'required|string',
            'background_value' => 'nullable|string',
            'login_button_background' => 'nullable|string',
            'login_button_color' => 'nullable|string',
            'login_button_shape' => 'nullable|string',
            'login_button_text' => 'nullable|string',
            'social_logins' => 'nullable|array',
            'login_method' => 'nullable|array',
            'password_method' => 'nullable|array',
            'registration_fields' => 'nullable|array',
            'faq' => 'nullable|boolean',
        ]);

        // Criando a customização de login
        $this->repository->create($data);

        // Retornando com sucesso via Inertia e preservando o estado da página
        return redirect()->route('login_customizations.index')->with('success', 'Login customization created successfully!');
    }
}
