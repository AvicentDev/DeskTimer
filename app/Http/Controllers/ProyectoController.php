<?php

namespace App\Http\Controllers;

use App\Models\Miembro;
use App\Models\Proyecto;
use App\Models\ProyectoArchivado;
use App\Models\Tarea;
use App\Models\TareaArchivada;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProyectoController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $proyectos = Proyecto::where('usuario_id', $user->id)
            ->with(['cliente', 'miembros', 'tareas', 'entradasTiempo']);

        if ($request->has('cliente_id')) {
            $proyectos->where('cliente_id', $request->cliente_id);
        }

        return response()->json([
            'proyectos' => $proyectos->get()
        ]);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nombre'           => 'required|string|max:255',
            'descripcion'      => 'nullable|string',
            'tiempo_estimado'  => 'required|integer|min:1',
            'fecha_entrega'    => 'required|date',
            'estado'           => 'required|string|max:50',
            'prioridad'        => 'required|string|max:50',
            'cliente_id'       => 'nullable|exists:clientes,id',
            'color'            => 'nullable|string|max:20',
            'miembros'         => 'array',
            'miembros.*.id'    => 'exists:miembros,id',
            'miembros.*.rol'   => 'required|string'
        ]);

        $user = Auth::user();
        $fecha_creacion = Carbon::now();

        $proyecto = Proyecto::create([
            'nombre'          => $validatedData['nombre'],
            'descripcion'     => $validatedData['descripcion'] ?? null,
            'tiempo_estimado' => $validatedData['tiempo_estimado'],
            'fecha_entrega'   => $validatedData['fecha_entrega'],
            'fecha_creacion'  => $fecha_creacion,
            'estado'          => $validatedData['estado'],
            'prioridad'       => $validatedData['prioridad'],
            'cliente_id'      => $validatedData['cliente_id'] ?? null,
            'color'           => $validatedData['color'] ?? null,
            'usuario_id'      => $user->id
        ]);

        if (!empty($validatedData['miembros'])) {
            foreach ($validatedData['miembros'] as $miembro) {
                $proyecto->miembros()->attach($miembro['id'], ['rol' => $miembro['rol']]);
            }
        }

        return response()->json([
            'message'  => 'Proyecto creado exitosamente',
            'proyecto' => $proyecto
        ], 201);
    }

    public function update(Request $request, Proyecto $proyecto)
    {
        $validatedData = $request->validate([
            'nombre'          => 'required|string|max:255',
            'descripcion'     => 'nullable|string',
            'tiempo_estimado' => 'required|integer|min:1',
            'fecha_entrega'   => 'required|date',
            'estado'          => 'required|string|max:50',
            'prioridad'       => 'required|string|max:50',
            'cliente_id'      => 'nullable|exists:clientes,id',
            'color'           => 'nullable|string|max:20',
        ]);

        $proyecto->update([
            'nombre'         => $validatedData['nombre'],
            'descripcion'    => $validatedData['descripcion'] ?? null,
            'tiempo_estimado'=> $validatedData['tiempo_estimado'],
            'fecha_entrega'  => $validatedData['fecha_entrega'],
            'estado'         => $validatedData['estado'],
            'prioridad'      => $validatedData['prioridad'],
            'cliente_id'     => $validatedData['cliente_id'] ?? null,
            'color'          => $validatedData['color'] ?? null,
        ]);

        return response()->json([
            'message' => 'Proyecto actualizado exitosamente',
            'proyecto' => $proyecto
        ], 200);
    }

    public function destroy(Proyecto $proyecto)
    {
        try {
            $proyecto->tareas()->delete();
            $proyecto->delete();

            return response()->json(['message' => 'Proyecto eliminado exitosamente'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al eliminar el proyecto', 'error' => $e->getMessage()], 500);
        }
    }

    public function miembrosproyectos($proyecto_id)
    {
        $proyecto = Proyecto::with('miembros')->find($proyecto_id);
        if (!$proyecto) {
            return response()->json([
                'message' => 'El proyecto no existe o el id es inválido'
            ], 400);
        }
        return response()->json($proyecto->miembros, 200);
    }

    public function agregarMiembros(Request $request, Proyecto $proyecto)
    {
        $validatedData = $request->validate([
            'miembros' => 'required|array',
            'miembros.*.id' => 'required|exists:miembros,id',
            'miembros.*.rol' => 'required|string|max:255',
        ]);

        foreach ($validatedData['miembros'] as $miembro) {
            $proyecto->miembros()->attach($miembro['id'], ['rol' => $miembro['rol']]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Miembros agregados correctamente.'
        ]);
    }

    public function eliminarMiembro(Proyecto $proyecto, Miembro $miembro)
    {
        $proyecto->miembros()->detach($miembro->id);

        return response()->json([
            'success' => true,
            'message' => 'Miembro eliminado correctamente.'
        ]);
    }

    public function archivar($id)
    {
        $proyecto = Proyecto::with(['tareas', 'miembros'])->findOrFail($id);

        DB::transaction(function () use ($proyecto) {
            foreach ($proyecto->tareas as $tarea) {
                TareaArchivada::create([
                    'tarea_id'      => $tarea->id,
                    'proyecto_id'   => $tarea->proyecto_id,
                    'titulo'        => $tarea->nombre ?? 'Sin título',
                    'descripcion'   => $tarea->descripcion ?? 'Sin descripción',
                    'fecha_limite'  => $tarea->fecha_limite,
                    'estado'        => 'archivado',
                    'usuario_id'    => $tarea->usuario_id ?? Auth::id(),
                ]);

                $tarea->delete();
            }

            ProyectoArchivado::create([
                'proyecto_id'    => $proyecto->id,
                'nombre'         => $proyecto->nombre,
                'descripcion'    => $proyecto->descripcion ?? 'Sin descripción',
                'tiempo_estimado'=> $proyecto->tiempo_estimado,
                'fecha_entrega'  => $proyecto->fecha_entrega,
                'fecha_creacion' => $proyecto->fecha_creacion,
                'estado'         => 'archivado',
                'prioridad'      => $proyecto->prioridad,
                'cliente_id'     => $proyecto->cliente_id,
                'color'          => $proyecto->color,
                'usuario_id'     => $proyecto->usuario_id ?? Auth::id(),
                'fecha_archivo'  => now(),
                'info_proyecto'  => json_encode([
                    'nombre'         => $proyecto->nombre,
                    'descripcion'    => $proyecto->descripcion ?? 'Sin descripción',
                    'tiempo_estimado'=> $proyecto->tiempo_estimado,
                    'fecha_entrega'  => $proyecto->fecha_entrega,
                    'fecha_creacion' => $proyecto->fecha_creacion,
                    'estado'         => 'archivado',
                    'prioridad'      => $proyecto->prioridad,
                    'cliente_id'     => $proyecto->cliente_id,
                    'color'          => $proyecto->color,
                    'miembros'       => $proyecto->miembros->map(fn($m) => ['id' => $m->id, 'rol' => $m->pivot->rol])
                ])
            ]);

            $proyecto->delete();
        });

        return response()->json(['message' => 'Proyecto archivado correctamente.']);
    }
}
