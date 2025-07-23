<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Miembro extends Model
{
    protected $table = 'miembros';
    protected $fillable = ['nombre', 'email', 'rol', 'usuario_id'];
    protected $hidden = ['created_at', 'updated_at'];

    public function proyectos()
    {
        return $this->belongsToMany(Proyecto::class, 'proyecto_miembro')->withPivot('rol')->withTimestamps();
    }
 
    
        // Relación con User
        public function usuario()
        {
            return $this->belongsTo(User::class, 'usuario_id');
        }
    

}
