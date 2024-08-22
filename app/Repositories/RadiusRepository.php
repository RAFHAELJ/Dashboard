<?php

// app/Repositories/RadiusRepository.php
namespace App\Repositories;

use App\Models\Radius;

class RadiusRepository implements RadiusRepositoryInterface {

    public function all() {
        return Radius::all();
    }

    public function find($id) {
        return Radius::find($id);
    }

    public function create(array $data) {
        return Radius::create($data);
    }

    public function update($id, array $data) {
        $radius = Radius::find($id);
        $radius->update($data);
        return $radius;
    }

    public function delete($id) {
        return Radius::destroy($id);
    }
}
