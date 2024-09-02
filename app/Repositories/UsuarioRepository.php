<?php

// app/Repositories/UsuarioRepository.php
namespace App\Repositories;

use App\Models\Usuario;

class UsuarioRepository implements UsuarioRepositoryInterface {

    public function all() {
        return Usuario::all();
    }

    public function find($id) {
        return Usuario::find($id);
    }

    public function create(array $data) {
        return Usuario::create($data);
    }

    public function update($id, array $data) {
        $usuario = Usuario::find($id);
        $usuario->update($data);
        return $usuario;
    }

    public function delete($id) {
        return Usuario::destroy($id);
    }
}
