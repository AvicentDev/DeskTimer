<?php

    namespace App\Http\Controllers;

use App\Models\Proyecto;
use App\Models\Tarea;
use App\Models\TareaArchivada;
use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

    class ProyectoArchivado extends Controller
    {
        public function index()
        {
            $user = Auth::user();
    
            $proyectosArchivados = \App\Models\ProyectoArchivado::where('usuario_id', $user->id)
                ->with('proyecto') // Asegúrate de que la relación existe en el modelo
                ->get();
    
            return response()->json([
                'proyectos' => $proyectosArchivados
            ]);
        }
    
        public function restaurar($id)
        {
            $proyectoArchivado = \App\Models\ProyectoArchivado::find($id);

            if (!$proyectoArchivado) {
                return response()->json(['message' => 'Proyecto archivado no encontrado'], 404);
            }

            DB::transaction(function () use ($proyectoArchivado) {
                // Decodificar la información del proyecto original
                $infoProyecto = json_decode($proyectoArchivado->info_proyecto, true);

                // Restaurar el proyecto
                $proyecto = Proyecto::create([
                    'nombre'          => $infoProyecto['nombre'],
                    'descripcion'     => $infoProyecto['descripcion'],
                    'tiempo_estimado' => $infoProyecto['tiempo_estimado'],
                    'fecha_entrega'   => $infoProyecto['fecha_entrega'],
                    'fecha_creacion'  => $infoProyecto['fecha_creacion'],
                    'estado'          => 'en_proceso',
                    'prioridad'       => $infoProyecto['prioridad'],
                    'cliente_id'      => $infoProyecto['cliente_id'],
                    'usuario_id'      => $proyectoArchivado->usuario_id ?? Auth::id()
                ]);

                // Restaurar tareas
                TareaArchivada::where('proyecto_id', $proyectoArchivado->proyecto_id)
                    ->chunk(50, function ($tareasArchivadas) use ($proyecto) {
                        foreach ($tareasArchivadas as $tarea) {
                            Tarea::create([
                                'proyecto_id'  => $proyecto->id,
                                'nombre'       => $tarea->titulo,
                                'descripcion'  => $tarea->descripcion,
                                'fecha_limite' => $tarea->fecha_limite,
                                'estado' => in_array($tarea->estado, ['pendiente', 'en_proceso', 'finalizada']) ? $tarea->estado : 'pendiente',
                                'usuario_id'   => $tarea->usuario_id ?? Auth::id()
                            ]);
                            $tarea->delete();
                        }
                    });

                // Restaurar miembros
                if (!empty($infoProyecto['miembros'])) {
                    foreach ($infoProyecto['miembros'] as $miembro) {
                        $proyecto->miembros()->attach($miembro['id'], ['rol' => $miembro['rol']]);
                    }
                }

                // Eliminar el proyecto archivado
                $proyectoArchivado->delete();
            });

            return response()->json(['message' => 'Proyecto restaurado correctamente']);
        }

        public function destroy($id)
        {
            $proyectoArchivado = \App\Models\ProyectoArchivado::find($id);

            if (!$proyectoArchivado) {
                return response()->json(['message' => 'Proyecto archivado no encontrado'], 404);
            }

            $proyectoArchivado->delete();

            return response()->json(['message' => 'Proyecto archivado eliminado correctamente']);
        }


    }
