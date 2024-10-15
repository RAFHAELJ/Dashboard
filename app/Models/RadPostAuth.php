<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RadPostAuth extends Model
{
    protected $table = 'radpostauth';
    protected $connection = 'radius'; // Nome da conexão que você configurou
    public $timestamps = false;
}
