<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MacHistory extends Model

{
    protected $connection = 'mysql';
    protected $table = 'mac_history';    
    protected $fillable = ['radio_id', 'mac_antigo', 'mac_novo','nome'];

    public function radio()
    {
        return $this->belongsTo(Radio::class);
    }
}