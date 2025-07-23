<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $table = 'clientes';
    protected $fillable = ['nombre', 'email', 'telefono', 'usuario_id'];
    protected $hidden = ['created_at', 'updated_at'];

    public function proyectos()
    {
        return $this->hasMany(Proyecto::class, 'cliente_id');
    }
}
