<?php
namespace App\Repositories;

use App\Models\Radio;

class RadioRepository implements RadioRepositoryInterface {

    public function all() {
        return Radio::all();
    }

    public function find($id) {
        return Radio::find($id);
    }

    public function create(array $data) {
        return Radio::create($data);
    }

    public function update($id, array $data) {
        $radio = Radio::find($id);
        $radio->update($data);
        return $radio;
    }

    public function delete($id) {
        return Radio::destroy($id);
    }
}
