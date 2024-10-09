<?php
// app/Http/Middleware/SetDynamicDatabaseConnection.php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\Traits\UsesDynamicDatabaseConnection;

class SetDynamicDatabaseConnection
{
    use UsesDynamicDatabaseConnection;

    public function handle($request, Closure $next)
    {
        $user = Auth::user();

        if ($user) {
            // Utilize a trait para definir a conexão com a região do usuário ou a região da sessão para o administrador
            $region = $user->isAdmin() ? session('regiao') : $user->regiao;
            $this->setRadiusConnection($user, $region);
        }

        return $next($request);
    }
}

