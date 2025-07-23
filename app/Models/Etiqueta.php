<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Etiqueta extends Model
{
    use HasFactory;

    /**
     * Tabla asociada.
     */
    protected $table = 'etiquetas';

    /**
     * Atributos que se pueden asignar masivamente.
     */
    protected $fillable = [
        'nombre',
        'usuario_id',
    ];

    /**
     * Una etiqueta pertenece a un usuario.
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    /**
     * Relación muchos-a-muchos con Entradas de Tiempo.
     */
    public function entradasTiempo()
    {
        return $this->belongsToMany(
            Entrada_Tiempo::class,
            'entrada_tiempo_etiqueta',  // tabla pivote
            'etiqueta_id',              // FK de este modelo en la pivote
            'entrada_tiempo_id'         // FK del modelo relacionado en la pivote
        );
    }
}
