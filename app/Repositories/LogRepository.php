<?php

namespace App\Repositories;

use App\Models\Log;

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
    public function getAllLogs($perPage = 10)
    {
        return Log::with('user')
            ->orderBy('data', 'desc')
            ->paginate($perPage);
    }
}
