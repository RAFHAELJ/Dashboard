<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;

class ActionsSeeder extends Seeder
{
    public function run()
    {
        // Array com todas as ações (permissões) comuns do sistema
        $actions = [
            'ler',         // Permissão para ler (view)
            'gravar',      // Permissão para criar (create)
            'atualizar',   // Permissão para atualizar (update)
            'excluir',     // Permissão para excluir (delete)
            'tudo'         // Permissão para acesso total
        ];

        // Inserir as ações no banco de dados
        foreach ($actions as $action) {
            Permission::create([
                'name' => $action,
            ]);
        }
    }
}
