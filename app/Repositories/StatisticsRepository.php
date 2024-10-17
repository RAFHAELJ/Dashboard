<?php

namespace App\Repositories;

use Carbon\Carbon;
use App\Models\RadAcct;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;

class StatisticsRepository
{
    public function getAccessStatistics($startDate, $endDate)
    {
        $dates = $this->dateRange($startDate, $endDate);
        $statistics = [];

        foreach ($dates as $date) {
            $totalAccesses = RadAcct::whereDate('acctstarttime', $date)
                ->count();

            $statistics[] = [
                'date' => $date,
                'total_accesses' => $totalAccesses
            ];
        }

        return $statistics;
    }

    public function getStorageUsage()
    {
        // Caminhos das pastas
        $storageBasePath = storage_path('app/public');
        $folders = ['images', 'capas', 'videos'];
    
        // Tamanho total e utilizado
        $totalSize = 106253455360; // Espelho do tamanho total do sistema para o cálculo
        $usedSize = 0;
    
        foreach ($folders as $folder) {
            $folderPath = $storageBasePath . '/' . $folder;
            if (file_exists($folderPath)) {
                $usedSize += $this->folderSize($folderPath);
            }
        }
    
        // Cálculo mais preciso da porcentagem de uso
        $usedPercentage = ($totalSize > 0) ? ($usedSize / $totalSize) * 100 : 0;
    
        return [
            'total' => $totalSize,
            'used' => $usedSize,
            'used_percentage' => round($usedPercentage, 5) // Arredondar para 5 casas decimais
        ];
    }
    
    
    

    protected function dateRange($start, $end)
    {
        return CarbonPeriod::create($start, $end)->toArray();
    }

    public function folderSize($dir)
    {
        $size = 0;
        foreach (glob(rtrim($dir, '/') . '/*', GLOB_NOSORT) as $file) {
            $size += is_file($file) ? filesize($file) : $this->folderSize($file);
        }
        return $size;
    }
}
