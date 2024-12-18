<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Auth;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $connection = 'mysql';
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
    public function regioes()
    {
        return $this->belongsTo(Regiao::class, 'regiao');
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'user_permissions')
                    ->withPivot('page_id')
                    ->with('pages')  // Carrega as páginas relacionadas
                    ->withTimestamps();
    }

    public function pages()
    {
        return $this->belongsToMany(Page::class, 'user_permissions')->withPivot('permission_id')->withTimestamps();
    }
    public function hasPermissionForPage(Page $page, $action)
{
    $permissions = $this->permissions()
                        ->wherePivot('page_id', $page->id)
                        ->get();

    return $permissions->contains('name', $action);
}
public function isAdmin()
{
    return $this->nivel === 'Administrador';
}

public function scopeByRegiao($query)
{
    if (Auth::check()) {
        if (!Auth::user()->isAdmin()) {
            // Limita a visualização apenas à mesma região do usuário logado
            $query->where('regiao', Auth::user()->regiao)
                  // Exclui administradores da listagem para usuários não-administradores
                  ->where('nivel', '!=', 'Administrador');
        }
    }
}
}
