<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Validation\ValidationException; // <-- AÑADIR ESTA LÍNEA

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // ==========================================================
        // INICIO DE LA CORRECCIÓN DEFINITIVA
        // ==========================================================
        try {
            // Se intenta autenticar al usuario. Si falla,
            // el método authenticate() lanzará una ValidationException.
            $request->authenticate();

        } catch (ValidationException $e) {

            // 1. Atrapamos la excepción de validación.
            // 2. Volvemos a colocar la "llave" en la sesión para que la
            //    redirección no sea bloqueada por nuestra seguridad.
            session()->flash('from_secret_url', true);

            // 3. Redirigimos manualmente a la ruta de login, pasando
            //    los errores y los datos de entrada (excepto la contraseña).
            return redirect()->route('login')
                ->withErrors(['email' => 'Correo o contraseña incorrectos.']) // Enviamos el mensaje personalizado
                ->withInput($request->except('password'));
        }
        // ==========================================================
        // FIN DE LA CORRECCIÓN
        // ==========================================================

        $request->session()->regenerate();

        return redirect()->intended(route('admin.panel', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}