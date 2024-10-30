<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    use HasFactory;

    protected $connection = 'mysql';
    protected $table = 'logs';
    protected $fillable = ['user_id', 'acao', 'regiao', 'data', 'descricao'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
