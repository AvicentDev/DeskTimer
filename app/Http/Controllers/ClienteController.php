<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Proyecto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClienteController extends Controller
{

    public function index()
    {
        $userId = Auth::id();
        $clientes = Cliente::with('proyectos')
                      ->where('usuario_id', $userId)
                      ->get();
    
        return response()->json($clientes);
    }
    

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nombre' => 'required|string|max:255',
            'telefono' => 'required|string|size:9',
            'email' => 'required|email|max:255',
            'usuario_id' => 'required|integer|exists:users,id',
        ]);

        $cliente = Cliente::create($validatedData);

        return response()->json([
            'message' => 'Cliente creado exitosamente',
            'cliente' => $cliente
        ], 201);
    }

    public function update(Request $request, Cliente $cliente)
    {
        $validatedData = $request->validate([
            'nombre' => 'required|string|max:255',
            'telefono' => 'required|string|size:9',
            'email' => 'required|email|max:255',
            'usuario_id' => 'required|integer|exists:users,id',
        ]);

        $cliente->update($validatedData);

        return response()->json([
            'message' => 'Cliente actualizado exitosamente',
            'cliente' => $cliente
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cliente $cliente)
    {
        if (!$cliente) {
            return response()->json([
                'message' => 'Cliente no encontrado'
            ], 404);
        }
        $cliente->delete();

        return response()->json([
            'message' => 'Cliente eliminado exitosamente'
        ], 200);
    }

    public function proyectoscliente(Request $request, $cliente_id)
    {
        $cliente = Cliente::find($cliente_id);
        if (!$cliente) {
            return response()->json([
                'message' => 'El cliente no existe o el id es inválido'
            ], 400);
        }

        // Obtener el usuario autenticado
        $user = Auth::user();

        $proyectos = Proyecto::where('usuario_id', $user->id)
            ->where('cliente_id', $cliente_id)
            ->with('cliente');

        // Ejecutar la consulta y retornar los datos
        return response()->json([
            'proyectos' => $proyectos->get()
        ]);
    }

    
  
}
