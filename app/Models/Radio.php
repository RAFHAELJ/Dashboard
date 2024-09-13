<?php

// app/Models/Radio.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Radio extends Model
{
    use HasFactory;

    protected $fillable = [
        'radio', 'mac', 'geo', 'endereco', 'info', 'regiao'
    ];

    public function regiao()
    {
        return $this->belongsTo(Regiao::class, 'regiao');
    }
}
