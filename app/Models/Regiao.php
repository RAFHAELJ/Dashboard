<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Regiao extends Model
{
    use HasFactory;
    protected $table = 'regioes';
    protected $connection = 'mysql';
    protected $fillable = ['cidade', 'bairros'];

    // Para lidar com o campo JSON de bairros
    protected $casts = [
        'bairros' => 'array',
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'regiao');
    }
    public function faq()
    {
        return $this->hasMany(Faq::class, 'regiao');
    }
    public function radios()
    {
        return $this->hasMany(Radio::class, 'regiao');
    }

    public function controladoras()
    {
        return $this->hasMany(Controladora::class, 'regiao');
    }
}

