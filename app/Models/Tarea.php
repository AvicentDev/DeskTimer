<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tarea extends Model
{
    protected $table = 'tareas';
    protected $fillable = ['nombre', 'descripcion', 'fecha_limite', 'estado', 'usuario_id', 'proyecto_id'];
    protected $hidden = ['created_at', 'updated_at'];

    public function usuario()
    {
        return $this->belongsTo(User::class);
    }

    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class);
    }
    
}
