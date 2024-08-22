<?php

// app/Repositories/CampanhaRepository.php
namespace App\Repositories;

use App\Models\Campanha;


class CampanhaRepository{

    public function all() {
        return Campanha::all();
    }

    public function find($id) {
        return Campanha::find($id);
    }

    public function create(array $data) {
        //dd($data);
        return Campanha::create($data);
    }

    public function update(Request $request, Campanha $user) {
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
          
        ]);
        return $user;
    }

    public function delete($id) {
        return Campanha::destroy($id);
    }
}
