<?php

// app/Models/Usuario.php
namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Usuario extends Model
{
    use HasFactory;

    protected $connection = 'mysql';
    protected $fillable = [
        'nome', 'email'
    ];
    protected static function booted()
    {
        if (Auth::check() && !Auth::user()->isAdmin()) {
            static::addGlobalScope('regiao', function (Builder $builder) {
                $builder->where('regiao', Auth::user()->regiao);
            });
        }
    }
    // Relacionamentos, se houver
}

