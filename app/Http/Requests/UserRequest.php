<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Altere isso conforme necessário para autorização
    }

    public function rules()
    {
        // Diferenciar as regras com base no método HTTP (POST para criação e PATCH para atualização)
        if ($this->isMethod('post')) {
            return [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:6|confirmed',
                'nivel' => 'required',
                'regiao' => 'required',
                'selectedActions' => 'nullable|array',
                'selectedPages' => 'nullable|array',
            ];
        }

        // Regras para o método PATCH (atualização)
        return [
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|string|email|max:255',
            'regiao' => 'sometimes|required',
            'selectedActions' => 'nullable|array',
            'selectedPages' => 'nullable|array',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'O nome é obrigatório.',
            'email.required' => 'O email é obrigatório.',
            'email.unique' => 'Este email já está em uso.',
            'password.required' => 'A senha é obrigatória.',
            'password.confirmed' => 'A confirmação de senha não corresponde.',
            'nivel.required' => 'O nível do usuário é obrigatório.',
            'regiao.required' => 'A região é obrigatória.',
        ];
    }
}
