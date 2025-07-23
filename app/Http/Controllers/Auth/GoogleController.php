<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    /**
     * Redirige al usuario a la página de OAuth de Google.
     */
    public function redirectToGoogle()
    {
        // Uso stateful para validar CSRF mediante parámetro state en sesión
        return Socialite::driver('google')
                        ->redirect();
    }

    /**
     * Maneja el callback tras la autenticación en Google.
     */
    public function handleGoogleCallback(Request $request)
    {
        try {
            // Obtiene el usuario autenticado desde Google usando la sesión
            $googleUser = Socialite::driver('google')->user();
    
            // Busca o crea el usuario en tu base de datos por su email
            $user = User::firstOrCreate(
                ['email' => $googleUser->getEmail()],
                [
                    'name'     => $googleUser->getName() ?? $googleUser->getNickname(),
                    'password' => Hash::make(Str::random(16)),
                    'rol'      => 'empleado',
                ]
            );
    
            // Crea un token de acceso con Sanctum
            $token = $user->createToken('auth_token')->plainTextToken;
    
            // URL del frontend (desde el archivo .env)
            $frontend = config('app.frontend_url', 'http://localhost:3000');
    
            // Redirige al frontend con el token como parámetro de consulta
            return redirect()->away("{$frontend}/auth/google/callback?token={$token}");
        } catch (\Exception $e) {
            // Retorna un JSON de error si algo falla
            return response()->json([
                'message' => 'Error al autenticar con Google',
                'error'   => $e->getMessage(), // Para desarrollo, quitar en producción
            ], 500);
        }
    }
    
    
}
