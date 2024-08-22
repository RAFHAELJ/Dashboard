<?php

// app/Models/Radius.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Radius extends Model
{
    use HasFactory;

    protected $connection = 'radius'; // Conexão separada para o banco de dados Radius

    // Defina os atributos e relacionamentos conforme necessário
}

