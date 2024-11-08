<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Faq extends Model
{
    use HasFactory;

    protected $connection = 'mysql';
    protected $table = 'faq';
    protected $fillable = ['texto','nome','regiao'];

    public function regiao()
    {
        return $this->belongsTo(Regiao::class, 'id');
    }

}
