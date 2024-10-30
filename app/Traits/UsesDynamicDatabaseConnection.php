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
    public function setRadiusConnection($user, $region = null, $type = null)
    {
        try {
            
            $regionToUse = $type === 'hotspot' ? $region : ($user->isAdmin() ? $region : $user->regiao);

            // Busca única com base na região definida
            $connection = AccessData::where('type', 'database')
                ->where('regiao', $regionToUse)
                ->first();
            
            

            if ($connection) {
                // Configura dinamicamente a conexão `dynamic_radius`
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
                return true;
            } else {
                // Caso não encontre a conexão
                throw new Exception("Nenhuma conexão encontrada para a região especificada.");
            }
        } catch (Exception $e) {
            // Loga o erro para o log do servidor
            Log::error("Erro ao definir a conexão dinâmica: " . $e->getMessage());
            return false;
        }
    }
}
