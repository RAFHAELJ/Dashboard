<?php

namespace App\Traits;

use App\Models\AccessData;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Exception;

trait UsesDynamicDatabaseConnection
{
    public function setRadiusConnection($user,$region = null)
    {
        
       
        // Se o usuário for administrador e uma região for fornecida, ele pode acessar qualquer base `radius`
        if ($user->isAdmin()) {
                       
            $connection = AccessData::where('type', 'database')
                ->where('regiao', $region)
                ->first();
               // dd($connection);
        } else {
            // Usuários comuns acessam apenas a base `radius` de sua própria região
            $connection = AccessData::where('type', 'database')
                ->where('regiao', $user->regiao)
                ->first();
        }

        if ($connection) {
            // Define dinamicamente a conexão `dynamic_radius`
            Config::set('database.connections.dynamic_radius', [
                'driver' => 'mysql',
                'host' => $connection->db_host,
                'port' => $connection->porta,
                'database' => $connection->db_name,
                'username' => $connection->db_user,
                'password' => $connection->db_password,
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
                'prefix' => '',
                'strict' => true,
                'engine' => null,
            ]);

            // Define a conexão ativa como `dynamic_radius`
            DB::setDefaultConnection('dynamic_radius');
        } else {
            // Se não houver conexão para a região, usa a conexão padrão
            DB::setDefaultConnection('radius');
            throw new Exception('Base de dados fora do ar ou não cadastrada. Entre em contato com o administrador.');

        }
    }
}
