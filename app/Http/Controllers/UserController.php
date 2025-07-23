<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request; // Asegúrate de importar la clase correcta
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
    
        // Validar sólo name, email y (opcional) password
        $validatedData = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email,' . $user->id . ',id',
            'password' => 'nullable|string|min:8|confirmed',
        ]);
    
        // Asignar los campos recibidos
        $user->name  = $validatedData['name'];
        $user->email = $validatedData['email'];
    
        if (!empty($validatedData['password'])) {
            $user->password = bcrypt($validatedData['password']);
        }
    
        if ($user->isDirty()) {
            $user->save();
        } else {
            return response()->json([
                'message' => 'No se realizaron cambios en el usuario'
            ], 200);
        }
    
        return response()->json([
            'user'    => $user,
            'message' => 'Usuario actualizado correctamente'
        ], 200);
    }
    

    public function changePassword(Request $request, $id)
    {
        // 1. Recupera el usuario
        $user = User::findOrFail($id);

        // 2. Valida la petición
        $validated = $request->validate([
            'current_password'      => ['required', 'string'],
            'password'              => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        // 3. Verifica que la contraseña actual coincida
        if (! Hash::check($validated['current_password'], $user->password)) {
            return response()->json([
                'message' => 'La contraseña actual no coincide'
            ], 422);
        }

        // 4. Asigna y guarda la nueva contraseña
        $user->password = Hash::make($validated['password']);
        $user->save();

        // 5. Respuesta de éxito
        return response()->json([
            'message' => 'Contraseña actualizada correctamente'
        ], 200);
    }
}
