<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Regiao extends Model
{
    use HasFactory;
    protected $table = 'regioes';
    protected $fillable = ['cidade', 'bairros'];

    // Para lidar com o campo JSON de bairros
    protected $casts = [
        'bairros' => 'array',
    ];
}

