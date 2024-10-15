<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RadGroupCheck extends Model
{
    protected $table = 'radgroupcheck';
    protected $connection = 'dynamic_radius'; // Nome da conexão que você configurou
    public $timestamps = false;
}
