<?php

// app/Models/Usuario.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
    use HasFactory;

    protected $connection = 'mysql';
    protected $fillable = [
        'nome', 'email'
    ];

    // Relacionamentos, se houver
}

