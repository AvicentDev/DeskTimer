<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    /**
     * Maneja la petición entrante.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role  El rol requerido para acceder al recurso
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string $role)
    {
        // Verifica que el usuario esté autenticado y tenga el rol requerido.
        if (!$request->user() || $request->user()->rol !== $role) {
            return response()->json([
                'error' => 'No tienes permiso para realizar esta acción.'
            ], 403);
        }

        return $next($request);
    }
}
