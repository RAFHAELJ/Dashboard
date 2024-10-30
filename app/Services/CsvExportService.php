<?php

namespace App\Services;

use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;

class CsvExportService
{
    /**
     * Gera e faz o download de um CSV.
     *
     * @param string $filename
     * @param array $headers
     * @param \Illuminate\Support\Collection $data
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function downloadCsv($filename, $headers, $data)
    {
        $callback = function() use ($headers, $data) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $headers);

            foreach ($data as $row) {
                fputcsv($file, $row->toArray());
            }

            fclose($file);
        };

        return Response::stream($callback, 200, [
            "Content-Type" => "text/csv",
            "Content-Disposition" => "attachment; filename={$filename}.csv"
        ]);
    }
}
