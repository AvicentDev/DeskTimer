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
        try {
            // Log para debug
            \Log::info('Intentando redirect a Google', [
                'client_id' => config('services.google.client_id'),
                'redirect' => config('services.google.redirect'),
                'has_secret' => !empty(config('services.google.client_secret'))
            ]);
            
            // Uso stateful para validar CSRF mediante parámetro state en sesión
            return Socialite::driver('google')->redirect();
            
        } catch (\Exception $e) {
            \Log::error('Error en redirectToGoogle', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            return response()->json([
                'error' => 'Error al iniciar autenticación con Google',
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => basename($e->getFile())
            ], 500);
        }
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
            $frontend = env('FRONTEND_URL', 'http://localhost:5173');
    
            // Redirige al frontend con el token como parámetro de consulta
            return redirect()->away("{$frontend}/auth/google/callback?token={$token}");
        } catch (\Exception $e) {
            // Log del error completo
            \Log::error('Error en Google OAuth', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Redirigir al frontend con el error
            $frontend = env('FRONTEND_URL', 'http://localhost:5173');
            return redirect()->away("{$frontend}/login?error=google_auth_failed&message=" . urlencode($e->getMessage()));
        }
    }
    
    
}
