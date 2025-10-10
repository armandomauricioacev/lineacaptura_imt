<?php

use App\Http\Controllers\LineaCapturaController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/inicio');
});

Route::get('/inicio', [LineaCapturaController::class, 'index'])->name('inicio');

Route::middleware(['web', 'ensure.step'])->group(function () {
    Route::get('/tramite', [LineaCapturaController::class, 'showTramite'])->name('tramite.show');
    Route::get('/persona', [LineaCapturaController::class, 'showPersonaForm'])->name('persona.show');
    Route::get('/pago', [LineaCapturaController::class, 'showPagoPage'])->name('pago.show');
});

Route::post('/tramite', [LineaCapturaController::class, 'showTramite'])->name('tramite.store');
Route::post('/persona', [LineaCapturaController::class, 'storeTramiteSelection'])->name('persona.store');
Route::post('/pago', [LineaCapturaController::class, 'storePersonaData'])->name('pago.store');
Route::post('/generar-linea', [LineaCapturaController::class, 'generarLineaCaptura'])->name('linea.generar');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

// BLOQUEAR URLs PROHIBIDAS - debe ir ANTES de la ruta catch-all
Route::get('/{any}', function ($any) {
    // Lista de URLs prohibidas (acceso directo bloqueado)
    $blocked = [
        'login', 
        'register', 
        'admin', 
        'administrador', 
        'dashboard', 
        'panel', 
        'admin-login-form',        // ← BLOQUEADO
        'admin-register-form',     // ← BLOQUEADO (NUEVO)
        'admin-dashboard-panel',
        'forgot-password',
    ];
    
    if (in_array($any, $blocked)) {
        abort(404); // Mostrar 404 en lugar de redirigir
    }
    
    abort(404);
})->where('any', '.*');