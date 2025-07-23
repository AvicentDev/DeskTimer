<?php

namespace App\Http\Controllers;

use App\Models\Tarea;
use App\Models\TareaArchivada;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TareasArchivadas extends Controller
{
    public function index(Request $request)
    {
        try {
            // Obtener el usuario autenticado
            $user = Auth::user();

            // Validamos que haya un usuario activo
            if (!$user) {
                return response()->json(['error' => 'Usuario no autenticado'], 401);
            }

            // Traer las tareas archivadas del usuario junto con la relación proyecto
            $tareas = TareaArchivada::where('usuario_id', $user->id)->with('proyecto')->get()->map(function ($tarea) {
                $tarea->nombre = $tarea->titulo; // Crear el "nombre" a partir de "titulo"
                return $tarea;
            });
            

            // Si no hay tareas, devolvemos un mensaje claro
            if ($tareas->isEmpty()) {
                return response()->json(['message' => 'No hay tareas archivadas'], 200);
            }

            // Retornamos las tareas con éxito
            return response()->json([
                'message' => 'Tareas archivadas obtenidas correctamente',
                'tareas' => $tareas
            ], 200);

        } catch (\Exception $e) {
            // Captura cualquier error inesperado
            return response()->json([
                'error' => 'Error al obtener las tareas archivadas',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    public function restaurar($id)
    {
        // Buscar la tarea archivada por ID
        $tareaArchivada = TareaArchivada::find($id);

        // Verificar si existe
        if (!$tareaArchivada) {
            return response()->json(['message' => 'Tarea archivada no encontrada'], 404);
        }

        // Usar una transacción para asegurar consistencia de datos
        DB::transaction(function () use ($tareaArchivada) {
            // Crear la nueva tarea con los datos de la tarea archivada
            $tarea = Tarea::create([
                'proyecto_id'  => $tareaArchivada->proyecto_id,
                'nombre'       => $tareaArchivada->titulo,
                'descripcion'  => $tareaArchivada->descripcion,
                'fecha_limite' => $tareaArchivada->fecha_limite,
                'estado'       => 'pendiente',
                'usuario_id'   => $tareaArchivada->usuario_id ?? Auth::id(),
            ]);

            // Si tenías campos adicionales en TareaArchivada, mapealos aquí
            // $tarea->otro_campo = $tareaArchivada->otro_campo;
            // $tarea->save();

            // Eliminar el registro archivado
            $tareaArchivada->delete();
        });

        return response()->json(['message' => 'Tarea restaurada correctamente']);
    }

    public function destroy(TareaArchivada $tarea_archivada)
    {
        // Verificar si la tarea archivada pertenece al usuario autenticado
        if ($tarea_archivada->usuario_id !== Auth::id()) {
            return response()->json(['message' => 'No tienes permiso para eliminar esta tarea archivada'], 403);
        }

        // Eliminar la tarea archivada
        $tarea_archivada->delete();

        return response()->json(['message' => 'Tarea archivada eliminada correctamente']);
    }

}
