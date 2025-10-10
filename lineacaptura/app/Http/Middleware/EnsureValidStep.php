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
        $routeName = $request->route()->getName();

        // Si el usuario intenta ir a la vista de trámites...
        if ($routeName === 'tramite') {
            // ...debe haber seleccionado una dependencia primero.
            if (!$request->session()->has('dependenciaId')) {
                return redirect()->route('inicio');
            }
        }

        // Si el usuario intenta ir a la vista de persona...
        if ($routeName === 'persona.show') {
            // ...debe haber seleccionado al menos un trámite.
            if (!$request->session()->has('tramites_seleccionados')) {
                return redirect()->route('tramite');
            }
        }

        // Si el usuario intenta ir a la vista de pago...
        if ($routeName === 'pago.show') {
            // ...debe haber ingresado sus datos personales.
            if (!$request->session()->has('persona_data')) {
                return redirect()->route('persona.show');
            }
        }
        
        // Si todo está en orden, permite el paso.
        return $next($request);
    }
}