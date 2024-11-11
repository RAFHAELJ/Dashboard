<?php

// app/Repositories/UsuarioRepository.php
namespace App\Repositories;

use App\Models\RadAcct;
use App\Models\Usuario;
use App\Models\RadCheck;
use Illuminate\Http\Request;

class UsuarioRepository  {

    public function all(Request $request,int $per_page) {
     
        if ($request->has('search')) {
            $search = $request->search;
            return RadCheck::where(function($query) use ($search) {
                $query->where('username', 'like', "%{$search}%")
                      ->orWhere('value', 'like', "%{$search}%")
                      ->orWhere('nome', 'like', "%{$search}%")
                      ->orWhere('cpf', 'like', "%{$search}%");
            })->paginate($per_page);
            
    }   

        return RadCheck::paginate($per_page);
    }

    public function find($id) {
        return RadCheck::find($id);
    }

    public function create(array $data) {
        return RadCheck::create($data);
    }

    public function update( array $data, $id) {
       // dd($data);
        $usuario = RadCheck::find($id);
        $usuario->update($data);
        return $usuario;
    }

    public function delete($id) {
        return RadCheck::destroy($id);
    }
}
