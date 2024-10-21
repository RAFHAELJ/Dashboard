<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RadCheck extends Model
{
    protected $table = 'radcheck';
    protected $connection = 'dynamic_radius'; // Nome da conexão que você configurou
    public $timestamps = false;
    protected $fillable = [

        'id', 'email', 'nome', 'telefone', 'Value',
        
    ];
    
}
