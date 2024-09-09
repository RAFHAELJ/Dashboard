<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RadReply extends Model
{
    protected $table = 'radreply';
    protected $connection = 'radius'; // Nome da conexão que você configurou
    public $timestamps = false;
}
