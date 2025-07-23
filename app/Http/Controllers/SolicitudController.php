<?php

namespace App\Http\Controllers;

use App\Models\Entrada_Tiempo;
use Illuminate\Http\Request;
use App\Models\Solicitud;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SolicitudController extends Controller
{
    /**
     * Crear una nueva solicitud (empleado)
     */
    public function crear(Request $request)
    {
        if (Auth::user()->rol !== 'empleado') {
            return response()->json(['error' => 'No tienes permisos'], 403);
        }
    
        $data = $request->validate([
            'tiempo_inicio' => 'required|date',
            'tiempo_fin'    => 'required|date|after_or_equal:tiempo_inicio',
            'proyecto_id'   => 'required|exists:proyectos,id',
            'descripcion'   => 'nullable|string',
            'comentario'    => 'nullable|string',
        ]);
    
        $solicitud = Solicitud::create([
            'usuario_id'    => Auth::id(),
            'tiempo_inicio' => \Carbon\Carbon::parse($data['tiempo_inicio'], 'Europe/Madrid'),
            'tiempo_fin'    => \Carbon\Carbon::parse($data['tiempo_fin'], 'Europe/Madrid'),
            'proyecto_id'   => $data['proyecto_id'],
            'descripcion'   => $data['descripcion'] ?? null,
            'estado'        => 'pendiente',
            'comentario'    => $data['comentario'] ?? null,
        ]);
    
        return response()->json(['message' => 'Solicitud creada', 'solicitud' => $solicitud], 201);
    }
    
    
    /**
     * Listar todas las solicitudes (solo admin)
     */
    public function listar()
    {
        $solicitudes = Solicitud::with(['usuario', 'entradaTiempo'])->get();
        
        return response()->json($solicitudes);
    }

    /**
     * Ver una solicitud específica
     */
    public function ver($id)
    {
        $solicitud = Solicitud::with(['usuario', 'entradaTiempo'])->findOrFail($id);
        return response()->json($solicitud);
    }

    /**
     * Aprobar una solicitud (admin)
     */
    public function aprobar($id)
    {
        $user = Auth::user();
        if ($user->rol !== 'administrador') {
            return response()->json(['error' => 'No tienes permisos'], 403);
        }
        $solicitud = Solicitud::findOrFail($id);
        $solicitud->estado = 'aprobado';
        $solicitud->save();
    
        $duracion = \Carbon\Carbon::parse($solicitud->tiempo_inicio)
                      ->diffInSeconds(\Carbon\Carbon::parse($solicitud->tiempo_fin));
    
        $entrada = Entrada_Tiempo::create([
            'tiempo_inicio'  => $solicitud->tiempo_inicio,
            'tiempo_fin'     => $solicitud->tiempo_fin,
            'duracion'       => $duracion,
            'proyecto_id'    => $solicitud->proyecto_id,
            // <-- aquí usamos el usuario que solicitó, no el que aprueba
            'usuario_id'     => $solicitud->usuario_id,
            'estado'         => 'aprobada',
            'descripcion'    => $solicitud->descripcion,
            'tarea_id'       => null,
            'solicitud_id'   => $solicitud->id,
        ]);
    
        return response()->json([
            'message'        => 'Solicitud aprobada y entrada creada',
            'solicitud'      => $solicitud,
            'entrada_tiempo' => $entrada
        ], 200);
    }
    
    
    


    /**
     * Rechazar una solicitud (admin)
     */
    public function rechazar($id)
    {
        $solicitud = Solicitud::findOrFail($id);
        $solicitud->estado = 'rechazado';
        $solicitud->save();

        return response()->json(['message' => 'Solicitud rechazada', 'solicitud' => $solicitud]);
    }

    /**
     * Eliminar una solicitud (admin)
     */
    public function eliminar($id)
    {
        $solicitud = Solicitud::findOrFail($id);
        $solicitud->delete();

        return response()->json(['message' => 'Solicitud eliminada']);
    }

    
}

