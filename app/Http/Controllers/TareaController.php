<?php

namespace App\Http\Controllers;

use App\Models\Tarea;
use App\Models\TareaArchivada;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TareaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
             // Obtener el usuario autenticado
             $user = Auth::user();

             $tareas = Tarea::where('usuario_id', $user->id)->with('proyecto');
            
             // Ejecutar la consulta y retornar los datos
              return response()->json([
                'tareas' => $tareas->get()
            ]);

    }


    public function store(Request $request)
    {
            // Validar los datos de la request
            $validatedData = $request->validate([
                'nombre' => 'required|string|max:255',
                'descripcion' => 'nullable|string',
                'fecha_limite' => 'required|date',
                'estado' => 'required|string|max:50',
                'usuario_id' => 'required|exists:users,id',
                'proyecto_id' => 'nullable|integer|exists:proyectos,id',

            ]);

            // Obtener el usuario autenticado
            $user = Auth::user();

            // Crear la tarea con los datos validados
            $tarea = Tarea::create([
                'nombre' => $validatedData['nombre'],
                'descripcion' => $validatedData['descripcion'],
                'fecha_limite' => $validatedData['fecha_limite'],
                'estado' => $validatedData['estado'],
                'usuario_id' => $validatedData['usuario_id'],
                'proyecto_id' => $validatedData['proyecto_id'],
            ]);

            // Retornar la tarea creada
            return response()->json([
                'tarea' => $tarea,
                'message' => 'Tarea creada correctamente'
            ], 201);
    }

    public function update(Request $request, Tarea $tarea)
    {
        if (!$tarea) {
            return response()->json([
                'message' => 'Tarea no encontrada'
            ], 404);
        }

        // Validar los datos de la request
        $validatedData = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'fecha_limite' => 'required|date',
            'estado' => 'required|string|max:50',
            'usuario_id' => 'required|exists:users,id',
            'proyecto_id' => 'nullable|integer|exists:proyectos,id',
        ]);

        // Actualizar la tarea con los datos validados
        $tarea->nombre = $validatedData['nombre'];
        $tarea->descripcion = $validatedData['descripcion'];
        $tarea->fecha_limite = $validatedData['fecha_limite'];
        $tarea->estado = $validatedData['estado'];
        $tarea->usuario_id = $validatedData['usuario_id'];
        $tarea->proyecto_id = $validatedData['proyecto_id'];
        $tarea->save();

    
        return response()->json([
            'tarea' => $tarea,
            'message' => 'Tarea actualizada correctamente'
        ], 200);
    }

    public function destroy(Tarea $tarea)
    {
        if (!$tarea) {
            return response()->json([
            'message' => 'Tarea no encontrada'
            ], 404);
        }

        $tarea->delete();

        return response()->json([
            'message' => 'Tarea eliminada correctamente'
        ]);
    }

    public function tareasproyecto(Request $request, $proyecto_id)
    {

        $proyecto = \App\Models\Proyecto::find($proyecto_id);
        if (!$proyecto) {
            return response()->json([
            'message' => 'El proyecto no existe o el id es inválido'
            ], 400);
        }
        // Obtener el usuario autenticado
        $user = Auth::user();

        $tareas = Tarea::where('usuario_id', $user->id)
            ->where('proyecto_id', $proyecto_id)
            ->with('proyecto');

        // Ejecutar la consulta y retornar los datos
        return response()->json([
            'tareas' => $tareas->get()
        ]);
    }   

    public function archivar($id)
{
    try {
        // Aseguramos que el usuario está autenticado
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Usuario no autenticado'], 401);
        }

        // Recuperamos la tarea
        $tarea = Tarea::findOrFail($id);

        // Copiamos los datos necesarios a la tabla de tareas archivadas
        TareaArchivada::create([
            'tarea_id' => $tarea->id,
            'proyecto_id' => $tarea->proyecto_id,
            'titulo' => $tarea->nombre,
            'descripcion' => $tarea->descripcion,
            'estado' => 'archivado',
            'fecha_limite' => $tarea->fecha_limite,
            'fecha_archivo' => now(),
            'usuario_id' => $user->id, // Capturamos el ID del usuario autenticado
        ]);

        // Eliminamos la tarea original
        $tarea->delete();

        return response()->json(['message' => 'Tarea archivada correctamente.']);
    } catch (\Exception $e) {
        // Capturamos cualquier error inesperado y devolvemos un mensaje más claro
        return response()->json([
            'error' => 'Error al archivar la tarea',
            'details' => $e->getMessage()
        ], 500);
    }
}

}
