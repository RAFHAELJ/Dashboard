<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Card extends Model
{
    use HasFactory;

    protected $connection = 'mysql';
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
