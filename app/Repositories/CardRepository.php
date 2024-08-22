<?php

namespace App\Repositories;

use App\Models\Card;

class CardRepository implements CardRepositoryInterface
{
    protected $model;

    public function __construct(Card $model)
    {
        $this->model = $model;
    }

    public function all()
    {
        return $this->model->all();
    }

    public function find($id)
    {
        return $this->model->find($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update($id, array $data)
    {
        $card = $this->find($id);
        if ($card) {
            $card->update($data);
            return $card;
        }
        return null;
    }

    public function delete($id)
    {
        $card = $this->find($id);
        if ($card) {
            $card->delete();
            return true;
        }
        return false;
    }
}
