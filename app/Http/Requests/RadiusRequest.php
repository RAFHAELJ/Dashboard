<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RadiusRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        //\dd($this->all());
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            
                'nasname' => 'required|string|max:128',               
                'shortname' => 'required|string|max:32',
                'type' => 'required|string|max:32',
                'ports' => 'nullable|integer',
                'secret' => 'required|string|max:60',
                'community' => 'required|string|max:50',
                'description' => 'required|string|max:200',
            
        ];
    }
}
