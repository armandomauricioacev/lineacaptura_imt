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

Route::middleware('guest')->group(function () {
    // URL SECRETA: Redirige al login
    Route::get('ipanelmadmint', function () {
        // Marcar en la sesión que vino desde la URL correcta
        session(['from_secret_url' => true]);
        return redirect('/admin-login-form');
    });

    // Login real (URL oculta) - Solo accesible si viene de ipanelmadmint
    Route::get('admin-login-form', function () {
        // Verificar que vino desde la URL secreta
        if (!session('from_secret_url')) {
            abort(404); // Si no vino de ipanelmadmint, mostrar 404
        }
        session()->forget('from_secret_url'); // Limpiar la marca
        return app(AuthenticatedSessionController::class)->create();
    })->name('login');

    // Procesa el login (POST)
    Route::post('admin-login-form', [AuthenticatedSessionController::class, 'store']);

    Route::post('register', [RegisteredUserController::class, 'store']);

    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
        ->name('password.request');

    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
        ->name('password.email');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
        ->name('password.reset');

    Route::post('reset-password', [NewPasswordController::class, 'store'])
        ->name('password.store');
});

Route::middleware('auth')->group(function () {
    // Panel de administración (protegido) - Solo accesible si está autenticado
    Route::get('admin-dashboard-panel', function () {
        return view('admin');
    })->name('admin.panel');

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');

    Route::get('verify-email', EmailVerificationPromptController::class)
        ->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    Route::put('password', [PasswordController::class, 'update'])->name('password.update');
});