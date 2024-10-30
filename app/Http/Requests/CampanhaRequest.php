<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Intervention\Image\Facades\Image;

class CampanhaRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'nome' => 'required|string|max:255',
            'comeco' => 'required|date',
            'fim' => 'required|date|after_or_equal:comeco',
            'publico' => 'required|string',
            'minimo' => 'required|integer|min:0',
            'maxima' => 'required|integer|gte:minimo',
            'tipo' => 'required|string|in:video,imagem,formulario',
            'duracao' => 'required_if:tipo,video|integer|min:1',
            'url' => 'nullable|string',
            'urlForms' => 'nullable|string',
            'regiao' => 'nullable|string',
            'video' => 'nullable|file|mimes:mp4,avi,mov|max:10240',
            'capa' => 'nullable|file|mimes:jpeg,png,jpg|max:2048',
            'imagem' => 'nullable|file|mimes:jpeg,png,jpg|max:2048',
        ];
    }

    public function messages()
    {
        return [
            'nome.required' => 'O nome da campanha é obrigatório.',
            'comeco.required' => 'A data de início é obrigatória.',
            'fim.required' => 'A data de término é obrigatória.',
            'fim.after_or_equal' => 'A data de término deve ser igual ou posterior à data de início.',
            'minimo.required' => 'A idade mínima é obrigatória.',
            'maxima.required' => 'A idade máxima é obrigatória.',
            'maxima.gte' => 'A idade máxima deve ser maior ou igual à idade mínima.',
            'tipo.required' => 'O tipo de campanha é obrigatório.',
            'duracao.required_if' => 'A duração é obrigatória para campanhas de vídeo.',
            'video.mimes' => 'O vídeo deve ser do tipo: mp4, avi, ou mov.',
            'video.max' => 'O vídeo não pode ser maior que 10MB.',
            'capa.mimes' => 'A capa deve ser do tipo: jpeg, png, ou jpg.',
            'capa.max' => 'A capa não pode ser maior que 2MB.',
            'imagem.mimes' => 'A imagem deve ser do tipo: jpeg, png, ou jpg.',
            'imagem.max' => 'A imagem não pode ser maior que 2MB.',
            'capa.dimensions' => 'A capa deve ter exatamente 300x620 pixels.',
            'imagem.dimensions' => 'A imagem deve ter exatamente 340x620 pixels.',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Valida as dimensões da capa
            if ($this->hasFile('capa')) {
                $capa = $this->file('capa');
                $capaImage = Image::make($capa->getPathname());

                if ($capaImage->width() !== 300 || $capaImage->height() !== 620) {
                    $validator->errors()->add('capa', 'A capa deve ter exatamente 300x620 pixels.');
                }
            }

            // Valida as dimensões da imagem
            if ($this->hasFile('imagem')) {
                $imagem = $this->file('imagem');
                $imagemImage = Image::make($imagem->getPathname());

                if ($imagemImage->width() !== 340 || $imagemImage->height() !== 620) {
                    $validator->errors()->add('imagem', 'A imagem deve ter exatamente 340x620 pixels.');
                }
            }
        });
    }
}
