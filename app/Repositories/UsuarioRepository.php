<?php

// app/Repositories/UsuarioRepository.php
namespace App\Repositories;

use App\Models\RadAcct;
use App\Models\Usuario;
use App\Models\RadCheck;

class UsuarioRepository  {

    public function all(){        
        return RadCheck::paginate();
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
