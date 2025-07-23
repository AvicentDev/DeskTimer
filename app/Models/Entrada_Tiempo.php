<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Entrada_Tiempo extends Model
{
    use HasFactory;

    protected $table = 'entrada_tiempos';

    protected $fillable = [
        'tiempo_inicio',
        'tiempo_fin',
        'duracion',
        'descripcion',
        'tarea_id',
        'usuario_id',
        'proyecto_id',
        'solicitud_id',
        // ya no incluimos 'etiqueta_id'
    ];

    /**
     * Propiedades calculadas.
     */
    protected $appends = ['creation_method'];

    /**
     * Accessor para derivar el método de creación.
     */
    public function getCreationMethodAttribute()
    {
        return is_null($this->tiempo_fin) ? 'timer' : 'modal';
    }

    /**
     * Formato de serialización de fechas.
     */
    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    /**
     * Relaciones.
     */

    public function tarea()
    {
        return $this->belongsTo(Tarea::class, 'tarea_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class, 'proyecto_id');
    }

    public function solicitud()
    {
        return $this->belongsTo(Solicitud::class, 'solicitud_id');
    }

    /**
     * Relación muchos-a-muchos con Etiquetas.
     */
    public function etiquetas()
    {
        return $this->belongsToMany(
            Etiqueta::class,
            'entrada_tiempo_etiqueta',  // tabla pivote
            'entrada_tiempo_id',        // FK de este modelo en la pivote
            'etiqueta_id'               // FK del modelo relacionado en la pivote
        );
    }
}
