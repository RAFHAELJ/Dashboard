<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class PageController extends Controller
{
    public function edit($id)
    {
        // Aqui você deve buscar os dados da página pelo ID e passá-los para a view
        // Exemplo com dados fictícios:
        $pageData = [
            'id' => $id,
            'content' => '<h1>Bem-vindo!</h1><p>Conteúdo inicial da página.</p>',
        ];

        return Inertia::render('EditPage', [
            'page' => $pageData,
        ]);
    }

    public function update(Request $request, $id)
    {
        
        $request->validate([
            'content' => 'required|string',
        ]);

        return redirect()->route('edit.page', ['id' => $id])->with('success', 'Página atualizada com sucesso!');
    }
}
