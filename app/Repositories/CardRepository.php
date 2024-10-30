<?php

namespace App\Repositories;

use auth;
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
        $user_id = auth()->id(); // Obtém o ID do usuário autenticado
        return $this->card->where('user_id', $user_id)->get(); // Retorna apenas os registros do usuário autenticado
    }

    public function find($id)
    {
        return $this->card->find($id);
    }

    public function create(Request $request)
    {
        // Extrai os dados do request e cria o modelo
        $user_id = auth()->id();
       // dd($user_id);
        $data = $request->only([
            'title',
            'content',
            'url',
            'type',
            'chart_options',
            'format',
            'page',
           
        ]);
        $data['user_id'] = $user_id;

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
