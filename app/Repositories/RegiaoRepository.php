<?php

namespace App\Repositories;

use App\Models\Regiao;
use Illuminate\Http\Request;

class RegiaoRepository
{
    public function all(Request $request ,int $per_page)
    {
        if ($request->has('search')) {
            $search = $request->search;
            return Regiao::where(function($query) use ($search) {
                $query->where('cidade', 'like', "%{$search}%")
                      ->orWhere('bairros', 'like', "%{$search}%");
            })->paginate($per_page);
    }
        return Regiao::paginate($per_page);
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
