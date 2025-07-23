<?php

namespace App\Http\Controllers;

use App\Models\Entrada_Tiempo;
use App\Models\Etiqueta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EtiquetaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $userId = Auth::id();
        $etiquetas = Etiqueta::with('entradasTiempo')
                        ->where('usuario_id', $userId)
                        ->get();

        return response()->json($etiquetas);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nombre' => 'required|string|max:100',
        ]);

        $validatedData['usuario_id'] = Auth::id();
        $etiqueta = Etiqueta::create($validatedData);

        return response()->json([
            'message'  => 'Etiqueta creada exitosamente',
            'etiqueta' => $etiqueta
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Etiqueta $etiqueta)
    {
        if ($etiqueta->usuario_id !== Auth::id()) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        // Cargamos las entradas con la relación many-to-many
        $etiqueta->load('entradasTiempo');

        return response()->json($etiqueta);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Etiqueta $etiqueta)
    {
        if ($etiqueta->usuario_id !== Auth::id()) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $validatedData = $request->validate([
            'nombre' => 'required|string|max:100',
        ]);

        $etiqueta->update($validatedData);

        return response()->json([
            'message'  => 'Etiqueta actualizada exitosamente',
            'etiqueta' => $etiqueta
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Etiqueta $etiqueta)
    {
        if ($etiqueta->usuario_id !== Auth::id()) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $etiqueta->delete();

        return response()->json([
            'message' => 'Etiqueta eliminada exitosamente'
        ], 200);
    }

    /**
     * Display all tiempo entries for a given etiqueta.
     */
    public function entradas(Etiqueta $etiqueta)
    {
        if ($etiqueta->usuario_id !== Auth::id()) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        // Usamos la relación muchos-a-muchos para paginar
        $entradas = $etiqueta->entradasTiempo()->paginate(10);

        return response()->json([
            'entradas' => $entradas
        ]);
    }
}
