<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Proyecto extends Model
{

    protected $table = 'proyectos';
    protected $primaryKey = 'id';
    protected $fillable = [
        'nombre',
        'descripcion',
        'tiempo_estimado',
        'fecha_entrega',
        'fecha_creacion',
        'estado',
        'prioridad',
        'color',
        'cliente_id',
        'usuario_id',
    ];
    protected $hidden = ['created_at', 'updated_at'];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
    public function miembros()
    {
        return $this->belongsToMany(Miembro::class, 'proyecto_miembro')->withPivot('rol')->withTimestamps();
    } 
    public function tareas()
    {
        return $this->hasMany(Tarea::class, 'proyecto_id');
    }
    public function entradasTiempo()
    {
        return $this->hasMany(Entrada_Tiempo::class, 'proyecto_id');
    }

    public function tareasArchivadas()
{
    return $this->hasMany(TareaArchivada::class, 'proyecto_id');
}


}