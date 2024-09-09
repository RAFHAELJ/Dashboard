<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RadGroupReply extends Model
{
    protected $table = 'radgroupreply';
    protected $connection = 'radius'; // Nome da conexão que você configurou
    public $timestamps = false;
}
