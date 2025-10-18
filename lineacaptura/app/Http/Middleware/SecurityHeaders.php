<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // 🔒 Headers de Seguridad Básicos para Nivel Gubernamental
        $response->headers->set('X-Frame-Options', 'DENY'); // Prevenir clickjacking
        $response->headers->set('X-Content-Type-Options', 'nosniff'); // Prevenir MIME sniffing
        $response->headers->set('X-XSS-Protection', '1; mode=block'); // Protección XSS
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin'); // Control de referrer
        $response->headers->set('Permissions-Policy', 'geolocation=(), microphone=(), camera=()'); // Permisos restrictivos
        
        // 🛡️ Content Security Policy ajustado para recursos gubernamentales
        $csp = "default-src 'self'; " .
               "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://framework-gb.cdn.gob.mx; " .
               "font-src 'self' https://fonts.gstatic.com https://framework-gb.cdn.gob.mx; " .
               "img-src 'self' data: https://img.icons8.com https://framework-gb.cdn.gob.mx https://*.gob.mx; " .
               "script-src 'self' 'unsafe-inline' https://framework-gb.cdn.gob.mx; " .
               "connect-src 'self' https://*.gob.mx; " .
               "frame-ancestors 'none';";
        
        $response->headers->set('Content-Security-Policy', $csp);

        return $response;
    }
}
