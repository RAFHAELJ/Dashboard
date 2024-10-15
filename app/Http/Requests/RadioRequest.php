<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RadioRequest extends FormRequest
{
    public function authorize()
    {
        //\dd($this->all());
        return true;
    }

    public function rules()
    {
        return [
            'radio' => 'required|string|max:255',
            'mac' => 'required|string',
            'geo' => 'required|string|max:255',
            'endereco' => 'required|string|max:255',
            'info' => 'required|string|max:255',
            'controladora' => 'required|integer',
            'regiao' => 'required',
        ];
    }
}
