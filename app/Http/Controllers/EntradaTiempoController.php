<?php

namespace App\Http\Controllers;

use App\Models\Entrada_Tiempo;
use App\Models\Proyecto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class EntradaTiempoController extends Controller
{
    /**
     * Listar todas las entradas de tiempo.
     */
    public function index(Request $request)
    {
        $query = Entrada_Tiempo::with(['usuario', 'proyecto', 'etiquetas'])
            ->where('usuario_id', Auth::id());

        if ($request->has(['start', 'end'])) {
            $start = Carbon::parse($request->start)->startOfDay();
            $end = Carbon::parse($request->end)->endOfDay();
            $query->whereBetween('tiempo_inicio', [$start, $end]);
        }

        $entradas = $query->get();

        return response()->json($entradas);
    }

    /**
     * Iniciar el cronómetro.
     */
    public function iniciar(Request $request)
    {
        try {
            // Log para debugging
            \Log::info('Iniciando cronómetro', [
                'proyecto_id' => $request->proyecto_id,
                'usuario_id' => Auth::id(),
                'request_data' => $request->all()
            ]);

            $request->validate([
                'proyecto_id' => 'required|exists:proyectos,id',
                'descripcion' => 'nullable|string',
                'etiqueta_ids' => 'sometimes|array',
                'etiqueta_ids.*' => 'exists:etiquetas,id',
            ]);

            $horaLocal = Carbon::now('Europe/Madrid');

            $entrada = Entrada_Tiempo::create([
                'tiempo_inicio' => $horaLocal,
                'tiempo_fin' => null,
                'duracion' => 0,
                'proyecto_id' => $request->proyecto_id,
                'usuario_id' => Auth::id(),
                'tarea_id' => $request->tarea_id ?? null,
                'descripcion' => $request->descripcion ?? '',
            ]);

            // Sincronizar etiquetas
            $entrada->etiquetas()->sync($request->input('etiqueta_ids', []));

            $entrada->load('etiquetas');

            return response()->json([
                'message' => 'Cronómetro iniciado',
                'entrada_tiempo' => $entrada
            ], 201);

        } catch (\Exception $e) {
            \Log::error('Error al iniciar cronómetro', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'Error al iniciar cronómetro',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Detener el cronómetro.
     */
    public function detener(Request $request, $id)
    {
        try {
            $entrada = Entrada_Tiempo::findOrFail($id);

            if ($entrada->tiempo_fin) {
                return response()->json(['message' => 'El cronómetro ya fue detenido'], 400);
            }

            $inicio = Carbon::parse($entrada->tiempo_inicio, 'Europe/Madrid');
            $fin = Carbon::now('Europe/Madrid');
            $secs = (int) $inicio->diffInSeconds($fin);

            $entrada->update([
                'tiempo_fin' => $fin,
                'duracion' => $secs,
                'descripcion' => $request->input('descripcion', $entrada->descripcion ?? ''),
            ]);

            if ($request->has('etiqueta_ids')) {
                $entrada->etiquetas()->sync($request->input('etiqueta_ids', []));
            }

            $entrada->load('etiquetas');

            return response()->json([
                'message' => 'Cronómetro detenido',
                'entrada_tiempo' => $entrada
            ], 200);

        } catch (\Exception $e) {
            \Log::error('Error al detener cronómetro', [
                'entrada_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'Error al detener cronómetro',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Crear una nueva entrada de tiempo manualmente.
     */
    public function store(Request $request)
    {
        $request->validate([
            'tiempo_inicio' => 'required|date',
            'tiempo_fin' => 'nullable|date|after_or_equal:tiempo_inicio',
            'duracion' => 'required|integer|min:1',
            'proyecto_id' => 'required|exists:proyectos,id',
            'descripcion' => 'required|string',
            'etiqueta_ids' => 'sometimes|array',
            'etiqueta_ids.*' => 'exists:etiquetas,id',
        ]);

        $entrada = Entrada_Tiempo::create([
            'tiempo_inicio' => Carbon::parse($request->tiempo_inicio, 'Europe/Madrid'),
            'tiempo_fin' => $request->tiempo_fin
                ? Carbon::parse($request->tiempo_fin, 'Europe/Madrid')
                : null,
            'duracion' => $request->duracion,
            'proyecto_id' => $request->proyecto_id,
            'usuario_id' => Auth::id(),
            'tarea_id' => $request->tarea_id ?? null,
            'descripcion' => $request->descripcion,
        ]);

        $entrada->etiquetas()->sync($request->input('etiqueta_ids', []));
        $entrada->load('etiquetas');

        return response()->json([
            'message' => 'Entrada de tiempo creada',
            'entrada_tiempo' => $entrada
        ], 201);
    }

    /**
     * Actualizar una entrada de tiempo existente.
     */
    public function edit(Request $request, $id)
    {
        $entrada = Entrada_Tiempo::findOrFail($id);

        $request->validate([
            'tiempo_inicio' => 'required|date',
            'tiempo_fin' => 'nullable|date|after_or_equal:tiempo_inicio',
            'proyecto_id' => 'required|exists:proyectos,id',
            'descripcion' => 'nullable|string',
            'tarea_id' => 'nullable|exists:tareas,id',
            'etiqueta_ids' => 'sometimes|array',
            'etiqueta_ids.*' => 'exists:etiquetas,id',
        ]);

        $start = Carbon::parse($request->tiempo_inicio, 'Europe/Madrid');
        $end = $request->tiempo_fin
            ? Carbon::parse($request->tiempo_fin, 'Europe/Madrid')
            : null;
        $duration = $end
            ? $start->diffInSeconds($end)
            : $entrada->duracion;

        $entrada->update([
            'tiempo_inicio' => $start,
            'tiempo_fin' => $end,
            'duracion' => $duration,
            'proyecto_id' => $request->proyecto_id,
            'tarea_id' => $request->tarea_id,
            'descripcion' => $request->descripcion ?? $entrada->descripcion,
        ]);

        $entrada->etiquetas()->sync($request->input('etiqueta_ids', []));
        $entrada->load('etiquetas');

        return response()->json([
            'message' => 'Entrada de tiempo actualizada correctamente',
            'entrada_tiempo' => $entrada->fresh()
        ], 200);
    }

    public function updateDescripcion(Request $request, $id)
    {
        $entrada = Entrada_Tiempo::findOrFail($id);

        $validated = $request->validate([
            'descripcion' => 'nullable|string|max:255',
        ]);

        $entrada->descripcion = $validated['descripcion'] ?? $entrada->descripcion;
        $entrada->save();

        return response()->json($entrada);
    }

    /**
     * Eliminar una entrada de tiempo.
     */
    public function eliminar($id)
    {
        $entrada = Entrada_Tiempo::findOrFail($id);
        $entrada->delete();

        return response()->json([
            'message' => 'Entrada de tiempo eliminada correctamente.'
        ], 200);
    }

    /**
     * Usar una entrada de tiempo aprobada.
     */
    public function usarEntradaTiempo($id)
    {
        $entrada = Entrada_Tiempo::findOrFail($id);

        if ($entrada->estado !== 'aprobada') {
            return response()->json(['error' => 'Esta entrada de tiempo no está aprobada'], 403);
        }

        return response()->json(['message' => 'Entrada de tiempo utilizada correctamente']);
    }

    /**
     * Listar todas las entradas agrupadas por día.
     */
    public function listar()
    {
        $userId = Auth::id();

        $entradas = Entrada_Tiempo::where('usuario_id', $userId)
            ->with('etiquetas')
            ->orderBy('tiempo_inicio')
            ->get();

        $agrupadasPorDia = $entradas->groupBy(function ($entrada) {
            return Carbon::parse($entrada->tiempo_inicio, 'Europe/Madrid')
                ->format('Y-m-d');
        });

        $resultado = $agrupadasPorDia->map(function ($grupo, $dia) {
            return [
                'dia' => $dia,
                'entradas' => $grupo->values(),
                'total_tiempo' => $grupo->sum('duracion'),
            ];
        })->values();

        return response()->json($resultado);
    }

    /**
     * Crear entrada con tiempos (retorno detallado).
     */
    public function crearEntradaConTiempos(Request $request)
    {
        $request->validate([
            'tiempo_inicio' => 'required|date',
            'tiempo_fin' => 'required|date|after_or_equal:tiempo_inicio',
            'proyecto_id' => 'required|exists:proyectos,id',
            'descripcion' => 'nullable|string',
            'tarea_id' => 'nullable|exists:tareas,id',
        ]);

        $tz = 'Europe/Madrid';
        $tiempoInicio = Carbon::parse($request->tiempo_inicio, $tz);
        $tiempoFin = Carbon::parse($request->tiempo_fin, $tz);

        $entrada = Entrada_Tiempo::create([
            'tiempo_inicio' => $tiempoInicio,
            'tiempo_fin' => $tiempoFin,
            'duracion' => $tiempoInicio->diffInSeconds($tiempoFin),
            'proyecto_id' => $request->proyecto_id,
            'tarea_id' => $request->tarea_id,
            'usuario_id' => Auth::id(),
            'descripcion' => $request->descripcion,
        ]);

        $entrada->load(['proyecto.cliente', 'usuario', 'tarea', 'etiquetas']);

        return response()->json([
            'id' => $entrada->id,
            'tiempo_inicio' => $tiempoInicio->toDateTimeString(),
            'tiempo_fin' => $tiempoFin->toDateTimeString(),
            'duracion' => $entrada->duracion,
            'proyecto' => [
                'id' => $entrada->proyecto->id,
                'nombre' => $entrada->proyecto->nombre,
                'color' => $entrada->proyecto->color,
                'cliente' => [
                    'id' => $entrada->proyecto->cliente->id,
                    'nombre' => $entrada->proyecto->cliente->nombre
                ],
            ],
            'tarea' => $entrada->tarea ? [
                'id' => $entrada->tarea->id,
                'nombre' => $entrada->tarea->nombre
            ] : null,
            'organizer' => $entrada->usuario->name,
            'descripcion' => $entrada->descripcion,
            'etiquetas' => $entrada->etiquetas,
        ], 201);
    }

    /**
     * Reporte semanal de tiempo por proyecto.
     */
    public function reporteSemana(Request $request)
    {
        $request->validate([
            'start' => 'required|date',
            'end' => 'required|date|after_or_equal:start',
        ]);

        $start = Carbon::parse($request->start, 'Europe/Madrid')->startOfDay();
        $end = Carbon::parse($request->end, 'Europe/Madrid')->endOfDay();

        $raw = DB::table('entrada_tiempos')
            ->select(
                DB::raw("DATE(tiempo_inicio) as dia"),
                'proyecto_id',
                DB::raw("SUM(duracion) as segundos")
            )
            ->where('usuario_id', Auth::id())
            ->whereBetween('tiempo_inicio', [$start, $end])
            ->groupBy('dia', 'proyecto_id')
            ->orderBy('dia')
            ->get();

        // Lista de días
        $dias = [];
        for ($d = $start->copy(); $d->lte($end); $d->addDay()) {
            $dias[] = $d->format('Y-m-d');
        }

        $proyectos = Proyecto::with('cliente')
            ->whereIn('id', $raw->pluck('proyecto_id'))
            ->get()
            ->keyBy('id');

        $dataPorProyecto = [];
        foreach ($proyectos as $pid => $proj) {
            $dataPorProyecto[$pid] = [
                'proyecto_id' => $pid,
                'nombre' => $proj->nombre,
                'cliente_id' => $proj->cliente->id ?? null,
                'cliente_nombre' => $proj->cliente->nombre ?? null,
                'valores' => array_fill_keys($dias, 0),
                'total' => 0,
            ];
        }

        foreach ($raw as $r) {
            $horas = round($r->segundos / 3600, 2);
            $dataPorProyecto[$r->proyecto_id]['valores'][$r->dia] = $horas;
            $dataPorProyecto[$r->proyecto_id]['total'] += $horas;
        }

        $totalGlobal = array_sum(array_column($dataPorProyecto, 'total'));
        foreach ($dataPorProyecto as &$projData) {
            $projData['porcentaje'] = $totalGlobal
                ? round(100 * $projData['total'] / $totalGlobal, 1)
                : 0;
        }

        return response()->json([
            'dias' => $dias,
            'proyectos' => array_values($dataPorProyecto),
            'entradas' => $raw,
            'total' => round($totalGlobal, 2),
        ]);
    }

    /**
     * Asignar o sincronizar múltiples etiquetas a una entrada de tiempo.
     */
    public function asignarEtiquetas(Request $request, $id)
    {
        $request->validate([
            'etiqueta_ids' => 'required|array',
            'etiqueta_ids.*' => 'exists:etiquetas,id',
        ]);

        $entrada = Entrada_Tiempo::where('usuario_id', Auth::id())
            ->findOrFail($id);

        $entrada->etiquetas()->sync($request->etiqueta_ids);
        $entrada->load('etiquetas');

        return response()->json([
            'message' => 'Etiquetas asignadas correctamente',
            'entrada_tiempo' => $entrada
        ], 200);
    }

    public function getEtiquetas($id)
    {
        // Busca la entrada, cargando eager-load la relación etiquetas
        $entry = Entrada_Tiempo::with('etiquetas')
            ->where('usuario_id', Auth::id())
            ->findOrFail($id);

        // Retornamos únicamente el array de etiquetas
        return response()->json($entry->etiquetas);
    }

    public function desasignarEtiquetas(Request $request, $id)
    {
        $entrada = Entrada_Tiempo::where('usuario_id', Auth::id())
            ->findOrFail($id);

        // Si se pasan IDs específicos, se desasignan solo esas etiquetas
        if ($request->has('etiqueta_ids') && is_array($request->etiqueta_ids)) {
            $entrada->etiquetas()->detach($request->etiqueta_ids);
        } else {
            // Si no se especifican, se eliminan todas las etiquetas
            $entrada->etiquetas()->detach();
        }

        return response()->json(['message' => 'Etiquetas desasignadas correctamente.']);
    }

}
