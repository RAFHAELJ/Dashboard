<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    use HasFactory;

    protected $connection = 'mysql';
    protected $table = 'faq';
    protected $fillable = ['texto','nome'];

}
