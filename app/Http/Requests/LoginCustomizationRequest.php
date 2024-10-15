<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginCustomizationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * Pode ser true se não houver controle de autorização
     *
     * @return bool
     */
    public function authorize()
    {
       
        return true; // Aqui você pode adicionar lógica de autorização se necessário
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'layout_name' => 'required|string|max:255',
            'top_type' => 'required|string',
            'top_value' => 'nullable|string',
            'background_type' => 'required|string',
            'background_value' => 'nullable|string',
            'login_button_text' => 'required|string|max:255',
            'login_button_color' => 'nullable|string',
            'login_button_shape' => 'nullable|integer',
            'login_method' => 'nullable|array',
            'password_method' => 'nullable|array',
            'input_width' => 'nullable',
            'input_height' => 'nullable',
            'input_color' => 'nullable|string',
            'background_image' => 'nullable|string',
            'elements' => 'nullable|array',
            'region' => 'nullable|integer|max:255',
            'username' => 'nullable|string|max:255',
        ];
    }
    protected function prepareForValidation()
{
    $elements = $this->input('elements', []);
    foreach ($elements as &$element) {
        $element['id'] = (int) $element['id'];
        $element['top'] = (float) $element['top'];
        $element['left'] = (float) $element['left'];
        $element['width'] = (float) $element['width'];
        $element['height'] = (float) $element['height'];
        $element['shape'] = (int) $element['shape'];
        $element['opacity'] = (float) $element['opacity'];
        $element['elevation'] = (int) $element['elevation'];
       
    }

    $this->merge(['elements' => $elements]);
}


    /**
     * Custom messages for validation errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'layout_name.required' => 'O nome do layout é obrigatório.',
            'top_type.required' => 'O tipo do topo é obrigatório.',
            'background_type.required' => 'O tipo de fundo é obrigatório.',
            'login_button_text.required' => 'O texto do botão de login é obrigatório.',
            'region.required' => 'A região é obrigatória.',
            'username.required' => 'O nome de usuário é obrigatório.',
        ];
    }
}
