<?php

use App\Http\Controllers\LineaCapturaController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;

// Redirección inicial
Route::get('/', function () {
    return redirect('/inicio');
});

// Ruta pública de inicio
Route::get('/inicio', [LineaCapturaController::class, 'index'])->name('inicio');

// Rutas del flujo de captura (protegidas por middleware)
Route::middleware(['web', 'ensure.step'])->group(function () {
    Route::get('/tramite', [LineaCapturaController::class, 'showTramite'])->name('tramite.show');
    Route::get('/persona', [LineaCapturaController::class, 'showPersonaForm'])->name('persona.show');
    Route::get('/pago', [LineaCapturaController::class, 'showPagoPage'])->name('pago.show');
});

// Rutas POST para el flujo
Route::post('/tramite', [LineaCapturaController::class, 'showTramite'])->name('tramite.store');
Route::post('/persona', [LineaCapturaController::class, 'storeTramiteSelection'])->name('persona.store');
Route::post('/pago', [LineaCapturaController::class, 'storePersonaData'])->name('pago.store');
Route::post('/generar-linea', [LineaCapturaController::class, 'generarLineaCaptura'])->name('linea.generar');

// Ruta GET para generar-linea que redirige a inicio (protección contra recarga/navegación)
Route::get('/generar-linea', function () {
    return redirect('/inicio');
})->name('linea.generar.redirect');

// ==========================================================
// INICIO DE LA MODIFICACIÓN
// Nueva ruta para manejar el retroceso de forma segura.
// ==========================================================
Route::post('/regresar', [LineaCapturaController::class, 'regresar'])->name('regresar');
Route::get('/regresar', [LineaCapturaController::class, 'regresar'])->name('regresar.get');