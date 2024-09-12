<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Campanha extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'comeco',
        'fim',
        'publico',
        'idade',
        'tipo',
        'radios',
        'duracao',
        'imagem',        
        'video',
        'capa',
        'tempo',
        'url',
        'regiao'
    ];
}
