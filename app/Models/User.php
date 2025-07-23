<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasAttributes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable , HasAttributes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'rol',  // Campo para rol
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function proyectos()
    {
        return $this->hasMany(Proyecto::class, 'usuario_id');
    }

    // Métodos para verificar roles
    public function isEmpleado()
    {
        return $this->rol === 'empleado';
    }

    public function isAdministrador()
    {
        return $this->rol === 'administrador';
    }
}
