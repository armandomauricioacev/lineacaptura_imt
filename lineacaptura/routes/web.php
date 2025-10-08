<?php

use App\Http\Controllers\LineaCapturaController;
use Illuminate\Support\Facades\Route;

// Redirigir la ruta raíz (/) a /inicio
Route::get('/', function () {
    return redirect('/inicio');
});

// Ruta para mostrar la vista de inicio con las dependencias
Route::get('/inicio', [LineaCapturaController::class, 'index']);

// Ruta para mostrar los trámites de una dependencia específica
Route::match(['get', 'post'], '/tramite', [LineaCapturaController::class, 'showTramite'])->name('tramite');

// ✅ RUTAS MODIFICADAS
// POST para guardar la selección del trámite (desde /tramite) y redirigir
Route::post('/persona', [LineaCapturaController::class, 'storeTramiteSelection'])->name('persona.store');
// GET para mostrar el formulario de la persona (al regresar o después de la redirección)
Route::get('/persona', [LineaCapturaController::class, 'showPersonaForm'])->name('persona.show');

// Recibe los datos de la persona (desde /persona) y los guarda en sesión
Route::post('/pago', [LineaCapturaController::class, 'storePersonaData'])->name('pago.store');

// Muestra la página de resumen/pago con toda la información
Route::get('/pago', [LineaCapturaController::class, 'showPagoPage'])->name('pago.show');

// Ruta para el panel de administración
Route::get('/admin', function () {
    return view('admin');
});


// Muestra la página de resumen/pago con toda la información
Route::get('/pago', [LineaCapturaController::class, 'showPagoPage'])->name('pago.show');

// =========================================================================
//  NUEVA RUTA: Recibe la petición final para generar la línea de captura
// =========================================================================
Route::post('/generar-linea', [LineaCapturaController::class, 'generarLineaCaptura'])->name('linea.generar');