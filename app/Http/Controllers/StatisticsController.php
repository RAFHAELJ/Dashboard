<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Repositories\StatisticsRepository;

class StatisticsController extends Controller
{
    protected $statisticsRepository;

    public function __construct(StatisticsRepository $statisticsRepository)
    {
        $this->statisticsRepository = $statisticsRepository;
    }

    public function accessStatistics(Request $request)
    {
        $startDate = $request->input('startD', now()->subDays(10)->format('Y-m-d'));
        $endDate = $request->input('endD', now()->format('Y-m-d'));
        
        $cacheKey = "access_statistics_{$startDate}_{$endDate}";
        $cacheTime = 10; // Defina o tempo de cache em minutos
    
        // Utiliza o cache para armazenar as estatísticas de acessos
        $data = Cache::remember($cacheKey, $cacheTime, function () use ($startDate, $endDate) {
            return $this->statisticsRepository->getAccessStatistics($startDate, $endDate);
        });
    
        return response()->json(['success' => true, 'data' => $data]);
    }
    

    public function storageUsage()
{
    $cacheKey = 'storage_usage';
    $cacheTime = 10; // Defina o tempo de cache em minutos

    // Utiliza o cache para armazenar os dados de uso de armazenamento
    $usage = Cache::remember($cacheKey, $cacheTime, function () {
        return $this->statisticsRepository->getStorageUsage();
    });

    return response()->json(['success' => true, 'data' => $usage]);
}


    public function index()
    {
        return Inertia::render('Statistics/Overview');
    }
}
