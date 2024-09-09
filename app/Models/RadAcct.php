<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RadAcct extends Model
{
    protected $table = 'radacct';
    protected $connection = 'radius'; // Nome da conexão que você configurou
    public $timestamps = false; // Se a tabela não usa timestamps
}
