<?php

namespace App\Repositories;

use App\Models\Log;
use Illuminate\Http\Request;

class LogRepository
{
    /**
     * Insere um novo registro de log
     *
     * @param int|null $userId
     * @param string $acao
     * @param string|null $regiao
     * @return Log
     */
    public function createLog($userId, $acao, $regiao = null,$descricao = null)
    {
        return Log::create([
            'user_id' => $userId,
            'acao' => $acao,
            'regiao' => $regiao,
            'descricao' => $descricao,
            'data' => now(),
        ]);
    }

    /**
     * Busca os registros de log com paginação
     *
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAllLogs(Request $request,int $per_page)
    {
        if ($request->has('search')) {
            $search = $request->search;
            return Log::where(function($query) use ($search) {
                $query->Where('acao', 'like', "%{$search}%");
            })->paginate($per_page);
    }
        return Log::with('user')
            ->orderBy('data', 'desc')
            ->paginate($per_page);
    }
}
