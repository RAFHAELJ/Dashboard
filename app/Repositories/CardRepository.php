<?php

namespace App\Repositories;

use App\Models\Card;
use Illuminate\Http\Request;

class CardRepository implements CardRepositoryInterface
{
    protected $card;

    public function __construct(Card $card)
    {
        $this->card = $card;
    }

    public function all()
    {
        return $this->card->all();
    }

    public function find($id)
    {
        return $this->card->find($id);
    }

    public function create(Request $request)
    {
        // Extrai os dados do request e cria o modelo
        $data = $request->only([
            'title',
            'content',
            'url',
            'type',
            'chart_options',
            'page',
        ]);

        return $this->card->create($data);
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
