<?php
// app/Http/Middleware/SetDynamicDatabaseConnection.php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Traits\UsesDynamicDatabaseConnection;
use Inertia\Inertia;

class SetDynamicDatabaseConnection
{
    use UsesDynamicDatabaseConnection;

    public function handle($request, Closure $next)
    {
        $user = Auth::user();

        if ($user) {
            // Define a região de acordo com o tipo de usuário
            $region = $user->isAdmin() ? session('regiao') : $user->regiao;

            // Verifica se a conexão foi configurada corretamente
            $connected = $this->setRadiusConnection($user, $region);

            if (!$connected) {
                // Retorna uma resposta de erro amigável usando Inertia
                return Inertia::render('Error', [
                    'error' => 'Houve um problema ao acessar o banco de dados. Por favor, tente novamente mais tarde ou entre em contato com o suporte.'
                ]);
            }
        }

        return $next($request);
    }
}
