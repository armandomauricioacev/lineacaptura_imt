<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::middleware('guest')->group(function () {
    // URL SECRETA PARA LOGIN
    Route::get('ipanelmadmint', function () {
        session(['from_secret_url' => true]);
        return redirect('/admin-login-form');
    });

    Route::get('admin-login-form', function () {
        if (!session('from_secret_url')) {
            abort(404);
        }
        session()->forget('from_secret_url');
        return app(AuthenticatedSessionController::class)->create();
    })->name('login');

    Route::post('admin-login-form', [AuthenticatedSessionController::class, 'store']);

    // RUTAS DE RECUPERACIÓN DE CONTRASEÑA
    Route::get('forgot-password', function (Request $request) {
        $referer = $request->headers->get('referer');
        $path = $referer ? parse_url($referer, PHP_URL_PATH) : null;

        if ($path !== '/admin-login-form' && $path !== '/forgot-password') {
            abort(404);
        }

        return (new PasswordResetLinkController)->create();
    })->name('password.request');
    
    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
        ->name('password.email');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
        ->name('password.reset');
    Route::post('reset-password', [NewPasswordController::class, 'store'])
        ->name('password.store');
    
    // ==========================================================
    // INICIO DE LA CORRECCIÓN
    // Esta nueva ruta nos permite saltar de forma segura desde la
    // página de éxito al formulario de login.
    // ==========================================================
    Route::get('go-to-login', function () {
        // Marcamos la sesión como si viniéramos de la URL secreta
        session(['from_secret_url' => true]);
        // Redirigimos al formulario de login
        return redirect()->route('login');
    })->name('go-to-login');
    // ==========================================================
    // FIN DE LA CORRECCIÓN
    // ==========================================================
});

Route::middleware('auth')->group(function () {
    // Rutas de registro (solo para administradores autenticados)
    Route::get('admin-register-form', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('admin-register-form', [RegisteredUserController::class, 'store']);
    Route::get('admin-dashboard-panel', function () { return view('admin'); })->name('admin.panel');
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
    Route::get('verify-email', EmailVerificationPromptController::class)->name('verification.notice');
    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)->middleware(['signed', 'throttle:6,1'])->name('verification.verify');
    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])->middleware('throttle:6,1')->name('verification.send');
    Route::put('password', [PasswordController::class, 'update'])->name('password.update');
});