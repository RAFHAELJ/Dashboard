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

        //\dd($data);
        return Nas::create($data);
    }

    public function update($id, array $data) {
       // dd($data);
        $radius = Nas::find($id);
        $radius->update($data);
        return $radius;
    }

    public function delete($id) {
        return Nas::destroy($id);
    }
}
