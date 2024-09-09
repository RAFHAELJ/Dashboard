<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RadUserGroup extends Model
{
    protected $table = 'radusergroup';
    protected $connection = 'radius'; // Nome da conexão que você configurou
    public $timestamps = false;
}
