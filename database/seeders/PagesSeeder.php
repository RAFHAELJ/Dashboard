<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Page;

class PagesSeeder extends Seeder
{
    public function run()
    {
        // Array com todas as páginas do sistema ordenadas alfabeticamente pelo nome
        $pages = [
            ['name' => 'Campanhas Menu', 'slug' => 'campanhas'],
            ['name' => 'Configurar radios', 'slug' => 'configurarRadios'],
            ['name' => 'Dashboard', 'slug' => 'dashboard'],
            ['name' => 'FAQ', 'slug' => 'faq'],
            ['name' => 'Home', 'slug' => 'home'],
            ['name' => 'Incluir/listar Radio', 'slug' => 'incluirRadios'],
            ['name' => 'Login Customizations Index', 'slug' => 'login_customizations'],
            ['name' => 'Logs', 'slug' => 'logs'],
            ['name' => 'Mapa Radios', 'slug' => 'mapaRadios'],
            ['name' => 'Radius', 'slug' => 'radius'],
            ['name' => 'Radios Menu', 'slug' => 'radios'],
            ['name' => 'Rastrear Radio', 'slug' => 'rastrearRadio'],
            ['name' => 'Regiões Menu', 'slug' => 'regioes'],
            ['name' => 'Relatorio Radios', 'slug' => 'relatorioRadios'],
            ['name' => 'Usuarios Dash', 'slug' => 'usuariosDash'],
            ['name' => 'Usuarios Menu', 'slug' => 'users'],
            ['name' => 'Usuarios Radio', 'slug' => 'usuariosRadio'],
        ];

        // Inserir as páginas no banco de dados usando um loop
        foreach ($pages as $page) {
            Page::create([
                'name' => $page['name'],
                'slug' => $page['slug'],
            ]);
        }
    }
}
