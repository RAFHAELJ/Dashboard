<?php

// app/Models/Radio.php
namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RadioDash extends Model
{
    use HasFactory;

    protected $connection = 'mysql';
    protected $table = 'radios';
    protected $fillable = [
        'radio', 'mac', 'geo', 'endereco', 'info', 'regiao','controladora'
    ];

    public function regiao()
    {
        return $this->belongsTo(Regiao::class, 'regiao');
    }
    public function controladora()
    {
        return $this->belongsTo(AccessData::class, 'controladora');
    }

    protected static function booted()
    {
        static::addGlobalScope('regiao', function (Builder $builder) {
            if (Auth::check() && !Auth::user()->isAdmin()) {
                $builder->where('regiao', Auth::user()->regiao);
            }
        });
    }
}
