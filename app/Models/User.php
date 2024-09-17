<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'nivel',
        'regiao',

    
        
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function regiao()
    {
        return $this->belongsTo(Regiao::class, 'regiao');
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'user_permissions')->withPivot('page_id')->withTimestamps();
    }

    public function pages()
    {
        return $this->belongsToMany(Page::class, 'user_permissions')->withPivot('permission_id')->withTimestamps();
    }
}
