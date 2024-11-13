<?php

// app/Repositories/RadiusRepository.php
namespace App\Repositories;

use App\Models\Nas;

class RadiusRepository   {

    public function all() {
        return Nas::paginate();
    }

    public function find($id) {
        return Nas::find($id);
    }

    public function create(array $data) {
        // Verifica se o nasname já existe
        if (Nas::where('nasname', $data['nasname'])->exists()) {
            throw new \Exception("O nasname {$data['nasname']} já existe.");
        }
    
        return Nas::create($data);
    }

    public function update($id, array $data) {
        // Verifica se o nasname já existe em um registro diferente do atual
        $nasExistente = Nas::where('nasname', $data['nasname'])
                           ->where('id', '!=', $id)
                           ->exists();
    
        if ($nasExistente) {
            throw new \Exception("O nasname {$data['nasname']} já está em uso por outro registro.");
        }
    
        $radius = Nas::find($id);
        $radius->update($data);
        return $radius;
    }
    public function delete($id) {
        return Nas::destroy($id);
    }
}
