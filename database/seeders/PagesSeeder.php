<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Page;

class PagesSeeder extends Seeder
{
    public function run()
    {
        // Array com todas as páginas do sistema extraídas das rotas
        $pages = [
            // Páginas Globais
            'dashboard',
            'radius',
            'home',

            // Páginas de Usuários
            'users.index',
            'users.show',
            'users.create',
            'users.edit',

            // Páginas de Campanhas
            'campanhas.index',
            'campanhas.show',
            'campanhas.create',

            // Páginas de FAQ
            'faq.index',
            'faq.show',

            // Páginas de Regiões
            'regioes.index',
            'regioes.show',

            // Outras Páginas
            'profile.edit',
            'login_customizations.index',
        ];

        // Inserir as páginas no banco de dados
        foreach ($pages as $page) {
            Page::create([
                'name' => $page,
            ]);
        }
    }
}
