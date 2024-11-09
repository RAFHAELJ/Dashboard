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
            ['name' => 'Radios Menu', 'slug' => 'radios'],
            ['name' => 'Mapa Radios', 'slug' => 'mapaRadios'],
            ['name' => 'Rastrear Radio', 'slug' => 'rastrearRadio'],
            ['name' => 'Uso dos Radios', 'slug' => 'rastrearUsoRadio'],
            ['name' => 'Configurar radios', 'slug' => 'configurarRadios'],

            ['name' => 'Usuarios Menu', 'slug' => 'usuariosDash'],
            ['name' => 'Usuarios Radio', 'slug' => 'usuariosRadio'],
            ['name' => 'Usuarios Dash', 'slug' => 'users'],

            ['name' => 'Campanhas Menu', 'slug' => 'campanhas'],  
            ['name' => 'Login Customizations Index', 'slug' => 'login_customizations'],   
            ['name' => 'Controladoras', 'slug' => 'controladora'],
            ['name' => 'Radius', 'slug' => 'radius'],         
            ['name' => 'Dashboard', 'slug' => 'dashboard'],
            ['name' => 'FAQ', 'slug' => 'faq'],
            ['name' => 'Logs', 'slug' => 'logs'], 
            ['name' => 'Home', 'slug' => 'home'],
            ['name' => 'Incluir/listar Radio', 'slug' => 'incluirRadios'],        
            
            
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
