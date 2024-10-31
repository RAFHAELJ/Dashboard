<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccessData extends Model
{
    use HasFactory;

    protected $connection = 'mysql';
    protected $table = 'access_data';

    protected $fillable = [
        'type',
        'ip',
        'porta',
        'senha',
        'nome',
        'db_host',
        'db_name',
        'db_user',
        'db_password',
        'regiao',
        'info',
    ];
    protected $casts = [
        'regiao' => 'integer',
    ];

    // Definindo um escopo para cada tipo
    public function scopeController($query)
    {
        return $query->where('type', 'controller');
    }

    public function scopeDatabase($query)
    {
        return $query->where('type', 'database');
    }
    public function scopeRadius($query)
    {
        return $query->where('type', 'radius');
    }
    public function radios()
    {
        return $this->hasMany(RadioDash::class, 'id');
    }
    public function regiao()
    {
        return $this->belongsTo(Regiao::class, 'regiao');
    }
}
