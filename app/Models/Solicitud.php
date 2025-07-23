<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Solicitud extends Model
{
    use HasFactory;
    protected $table = 'solicitudes';

    protected $fillable = [
        'usuario_id',
        'tiempo_inicio',
        'tiempo_fin',
        'proyecto_id',
        'descripcion',
        'estado',
        'comentario'
    ];
    
    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
    
    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class, 'proyecto_id');
    }
    
    // Relación a la entrada creada tras aprobación (opcional)
    public function entradaTiempo()
    {
        return $this->hasOne(Entrada_Tiempo::class, 'solicitud_id');
    }
    
}

