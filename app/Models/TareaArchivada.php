<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TareaArchivada extends Model
{
    use HasFactory;

    protected $table = 'tareas_archivadas';

    protected $fillable = [
        'tarea_id',
        'proyecto_id',
        'titulo',
        'descripcion',
        'estado',
        'fecha_limite',
        'fecha_archivo',
        'usuario_id',
    ];

    // Relación con el Proyecto
    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class, 'proyecto_id');
    }

    // Relación con el Usuario (si la necesitas también)
    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
