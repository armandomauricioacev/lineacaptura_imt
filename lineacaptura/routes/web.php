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
Route::post('/regresar', [LineaCapturaController::class, 'regresar'])->name('regresar');

// Rutas de perfil (protegidas por auth)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Incluir rutas de autenticación (login, register, etc.)
require __DIR__.'/auth.php';

// ==========================================================
// RUTAS DEL PANEL DE ADMINISTRACIÓN CON CRUD
// ==========================================================
Route::middleware(['auth'])->group(function () {
    // Dashboard principal
    Route::get('/admin-dashboard-panel', [AdminController::class, 'index'])->name('admin.panel');
    
    // ========== DEPENDENCIAS ==========
    Route::post('/admin/dependencias', [AdminController::class, 'storeDependencia'])->name('admin.dependencias.store');
    Route::put('/admin/dependencias/{id}', [AdminController::class, 'updateDependencia'])->name('admin.dependencias.update');
    Route::delete('/admin/dependencias/{id}', [AdminController::class, 'destroyDependencia'])->name('admin.dependencias.destroy');
    
    // ========== TRÁMITES ==========
    Route::post('/admin/tramites', [AdminController::class, 'storeTramite'])->name('admin.tramites.store');
    Route::put('/admin/tramites/{id}', [AdminController::class, 'updateTramite'])->name('admin.tramites.update');
    Route::delete('/admin/tramites/{id}', [AdminController::class, 'destroyTramite'])->name('admin.tramites.destroy');
    
    // ========== LÍNEAS DE CAPTURA ==========
    Route::put('/admin/lineas-captura/{id}', [AdminController::class, 'updateLineaCaptura'])->name('admin.lineas.update');
    Route::delete('/admin/lineas-captura/{id}', [AdminController::class, 'destroyLineaCaptura'])->name('admin.lineas.destroy');
    
    // ========== USUARIOS ==========
    Route::put('/admin/usuarios/{id}', [AdminController::class, 'updateUser'])->name('admin.users.update');
    Route::delete('/admin/usuarios/{id}', [AdminController::class, 'destroyUser'])->name('admin.users.destroy');
});

// ==========================================================
// BLOQUEAR URLs PROHIBIDAS - DEBE IR AL FINAL
// ==========================================================
Route::get('/{any}', function ($any) {
    // Lista de URLs prohibidas (acceso directo bloqueado)
    $blocked = [
        'admin', 
        'administrador', 
        'dashboard', 
        'panel', 
        'admin-login-form',
        'admin-register-form',
    ];
    
    if (in_array($any, $blocked)) {
        abort(404); // Mostrar 404 para URLs bloqueadas
    }
    
    abort(404); // 404 para cualquier otra ruta no definida
})->where('any', '.*');