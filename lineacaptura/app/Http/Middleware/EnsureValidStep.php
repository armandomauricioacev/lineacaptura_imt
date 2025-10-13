<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureValidStep
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $currentRouteName = $request->route()->getName();
        $session = $request->session();

        // Determina el paso "correcto" en el que el usuario DEBERÍA estar.
        $correctStepRoute = 'inicio'; // Por defecto, todos deben estar en el inicio.

        if ($session->has('dependenciaId') && !$session->has('tramites_seleccionados')) {
            $correctStepRoute = 'tramite.show';
        }
        if ($session->has('tramites_seleccionados') && !$session->has('persona_data')) {
            $correctStepRoute = 'persona.show';
        }
        if ($session->has('persona_data')) {
            $correctStepRoute = 'pago.show';
        }

        // Excepción: Si el proceso ha finalizado, la única página válida es la de inicio.
        if ($session->has('linea_capturada_finalizada')) {
            $correctStepRoute = 'inicio';
        }

        // Si la ruta que el usuario intenta visitar NO es la que le corresponde...
        if ($currentRouteName !== $correctStepRoute) {
            // ...lo redirigimos a la fuerza a la página correcta.
            // Esto bloquea cualquier intento de navegar con la URL.
            return redirect()->route($correctStepRoute);
        }

        // Si está en la página correcta, le permitimos continuar.
        return $next($request);
    }
}