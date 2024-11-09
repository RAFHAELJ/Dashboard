<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AccessDataRequest extends FormRequest
{
    public function authorize()
    {
        //dd($this->all());
        return true;
    }

    public function rules()
    {
        return [
            'type' => 'required|in:controller,database,radius',
            'nome' => 'required|string',
            
            // Campos específicos para 'controller'
            'ip' => 'required_if:type,controller|nullable|string',
            'porta' => 'required_if:type,controller|nullable|integer',
            'senha' => 'required_if:type,controller|nullable|string',
            
            // Campos específicos para 'database'
            'db_host' => 'required_if:type,database|nullable|string',
            'db_name' => 'required_if:type,database|nullable|string',
            'db_user' => 'required_if:type,database|nullable|string',
            'db_password' => 'required_if:type,database|nullable|string',
    
            // Campos gerais
            'regiao' => 'required|integer',
            'info' => 'nullable',
        ];
    }
    
}
