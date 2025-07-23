<?php

// app/Models/ProyectoArchivado.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProyectoArchivado extends Model
{
    protected $table = 'proyectos_archivados';

    protected $fillable = [
        'proyecto_id',
        'nombre',
        'descripcion',
        'fecha_archivo',
        'usuario_id',
        'info_proyecto',
        // otros campos
    ];

    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class, 'proyecto_id');
    }

}

