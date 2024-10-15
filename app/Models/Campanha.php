<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Campanha extends Model
{
    use HasFactory;

    protected $connection = 'mysql';
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

    protected static function booted()
    {
        static::addGlobalScope('regiao', function (Builder $builder) {
            if (Auth::check() && !Auth::user()->isAdmin()) {
                $builder->where('regiao', Auth::user()->regiao);
            }
        });
    }
}
