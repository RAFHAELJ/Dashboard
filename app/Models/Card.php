<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Card extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'url',
        'type',
        'chartOptions',
        'page',
    ];

    protected $casts = [
        'chartOptions' => 'array',
    ];
}
