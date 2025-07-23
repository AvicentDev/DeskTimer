<?php

namespace App\Http\Controllers;

use App\Models\Miembro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MiembroController extends Controller
{
    /**
     * Display a listing of the resource.
     */public function index()
{
    $user = Auth::user();

    $miembros = Miembro::where('usuario_id', $user->id)->get();
    
    return response()->json($miembros);
}


    public function store(Request $request)
    {
        // Validar los datos de la request
        $validatedData = $request->validate([
            'nombre' => 'required|string|max:255',
            'email' => 'required|email',
            'rol' => 'nullable|string|in:administrador,desarrollador,diseñador,tester,otro',
        ]);

        // Obtener el usuario autenticado
        $user = Auth::user();

        // Crear el miembro con los datos validados
        $miembro = Miembro::create([
            'nombre' => $validatedData['nombre'],
            'email' => $validatedData['email'],
            'rol' => $validatedData['rol'],
            'usuario_id' => $user->id, // Añadir el usuario_id
        ]);

        return response()->json([
            'miembro' => $miembro,
            'message' => 'Miembro creado correctamente'
        ], 201);
    }

    public function update(Request $request, Miembro $miembro)
    {

        if ($miembro->usuario_id !== Auth::id()) {
            return response()->json([
                'message' => 'No tienes permisos para actualizar este miembro'
            ], 403);
        }
        // Validar los datos de la request
        $validatedData = $request->validate([
            'nombre' => 'required|string|max:255',
            'email' => 'required|email',
            'rol' => 'nullable|string|in:administrador,desarrollador,diseñador,tester,otro',
        ]);

        // Actualizar el miembro con los datos validados
        $miembro->update($validatedData);

        return response()->json([
            'miembro' => $miembro,
            'message' => 'Miembro actualizado correctamente'
        ], 200);
        
    }

    public function destroy(Miembro $miembro)
    {

        $miembro->delete();
        return response()->json([
            'message' => 'Miembro eliminado correctamente'
        ], 200);
    }

}
