<?php

namespace App\Repositories;

use App\Models\Regiao;

class RegiaoRepository
{
    public function all()
    {
        return Regiao::paginate();
    }

    public function create(array $data)
    {
        return Regiao::create($data);
    }

    public function update($id, array $data)
    {
        $regiao = Regiao::findOrFail($id);
        $regiao->update($data);
        return $regiao;
    }

    public function getRegiaoId($regiao)
    {
        $regiaoId = Regiao::whereRaw('LOWER(REPLACE(cidade, " ", "")) = ?', [strtolower(str_replace(' ', '', $regiao))])
        ->pluck('id')
        ->first(); 

        return $regiaoId;
    }

    public function delete($id)
    {
        $regiao = Regiao::findOrFail($id);
        return $regiao->delete();
    }

    public function find($id)
    {
        return Regiao::findOrFail($id);
    }
}
