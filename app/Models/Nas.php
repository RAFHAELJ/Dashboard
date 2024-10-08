<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Nas extends Model
{
    protected $table = 'nas';
    protected $connection = 'radius'; // Nome da conexão que você configurou
    public $timestamps = false;

    protected $fillable = [
        'id',
        'nasname',
        'shortname',
        'type',
        'ports',
        'secret',
        'community',
        'description',
    ];
}
