<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;


class AuthController extends Controller
{
    public function register(Request $request)
    {
        // Paso 3: Crear usuario en BD
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8',
                'rol' => 'required|string|in:empleado,administrador',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'mensaje' => 'Validación falló',
                    'errores' => $validator->errors()
                ], 422);
            }

            // Intentar crear el usuario
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'rol' => $request->rol,
            ]);

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'paso' => 3,
                'mensaje' => 'Usuario creado exitosamente en BD',
                'access_token' => $token,  // ✅ Cambiado a snake_case
                'token_type' => 'Bearer',
                'data' => [  // ✅ Envuelto en 'data' para consistencia
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'rol' => $user->rol
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error en paso 3 (crear usuario)',
                'mensaje' => $e->getMessage(),
                'linea' => $e->getLine(),
                'archivo' => basename($e->getFile())
            ], 500);
        }
    }


    public function login(Request $request)
    {
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $user = User::where('email', $request['email'])->firstOrFail();

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Hola ' . $user->name,
            'access_token' => $token,  // ✅ Cambiado a snake_case
            'token_type' => 'Bearer',
            'data' => $user,  // ✅ Cambiado 'user' a 'data' para consistencia
        ]);
    }

    public function logout(Request $request)
    {
        if ($request->user()) {
            $request->user()->tokens()->delete();
        }

        return response()->json(['message' => 'Logged out'], 200);
    }
}
