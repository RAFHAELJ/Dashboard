<?php

// app/Repositories/CampanhaRepository.php
namespace App\Repositories;

use App\Models\Campanha;


class CampanhaRepository{

    public function all() {
        return Campanha::paginate();
    }

    public function find($id) {
        return Campanha::find($id);
    }

    public function create(array $data) {
        //dd($data);
        return Campanha::create($data);
    }

    public function update(array $data,Campanha $campanha)
    {
        $campanha->update($data);
        return $campanha;
    }
    

    public function delete($id) {
        return Campanha::destroy($id);
    }
}
