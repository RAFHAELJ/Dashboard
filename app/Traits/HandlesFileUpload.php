<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;

trait HandlesFileUpload
{
    /**
     * Faz o upload de um arquivo e retorna o caminho salvo.
     *
     * @param  \Illuminate\Http\UploadedFile  $file
     * @param  string  $directory
     * @param  string  $disk
     * @return string
     */
    public function uploadFile($file, $directory = 'uploads', $disk = 'public')
    {
        // Armazena o arquivo e retorna o caminho
        return $file->store($directory, $disk);
    }

    /**
     * Exclui um arquivo do disco especificado.
     *
     * @param  string  $filePath
     * @param  string  $disk
     * @return bool
     */
    public function deleteFile($filePath, $disk = 'public')
    {
        // Verifica se o arquivo existe e o remove
        return Storage::disk($disk)->exists($filePath) && Storage::disk($disk)->delete($filePath);
    }

    /**
     * Verifica se um arquivo existe no disco especificado.
     *
     * @param  string  $filePath
     * @param  string  $disk
     * @return bool
     */
    public function fileExists($filePath, $disk = 'public')
    {
        return Storage::disk($disk)->exists($filePath);
    }

    /**
     * Faz o download de um arquivo do disco.
     *
     * @param  string  $filePath
     * @param  string  $disk
     * @return \Illuminate\Http\Response
     */
    public function downloadFile($filePath, $disk = 'public')
    {
        if ($this->fileExists($filePath, $disk)) {
            return Storage::disk($disk)->download($filePath);
        }
        
        return response()->json(['error' => 'Arquivo não encontrado.'], 404);
    }
}
