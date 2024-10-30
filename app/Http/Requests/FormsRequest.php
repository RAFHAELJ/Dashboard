<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FormsRequest extends FormRequest
{
    public function authorize()
    {
      //  \dd ($this->all());
        return true; // Permite que todos os usuários possam fazer esta requisição
    }

    public function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'questions' => 'required|array',
            'questions.*.text' => 'required|string',
            'questions.*.type' => 'required|string',
            'questions.*.options' => 'array',
        ];
    }

    public function messages()
    {
        return [
            'questions.required' => 'As perguntas são obrigatórias.',
            'questions.*.text.required' => 'O texto da pergunta é obrigatório.',
            'questions.*.type.required' => 'O tipo de resposta é obrigatório.',
        ];
    }
}
