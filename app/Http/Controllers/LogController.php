<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Repositories\LogRepository;

class LogController extends Controller
{
    protected $logRepository;

    public function __construct(LogRepository $logRepository)
    {
        $this->logRepository = $logRepository;
    }

    /**
     * Exibe a lista de logs.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Inertia\Response
     */
    public function index(Request $request)
    {
        $logs = $this->logRepository->getAllLogs($request,$request->input('per_page'));

        if ($request->wantsJson()) {
            return response()->json($logs);
        }

        return Inertia::render('logs/LogList', [
            'logs' => $logs
        ]);
    }
}
