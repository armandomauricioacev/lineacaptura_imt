<?php

namespace App\Http\Controllers;

use App\Models\Dependencia;
use App\Models\Tramite;
use App\Models\LineasCapturadas;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class LineaCapturaController extends Controller
{
    /**
     * /inicio
     */
    public function index(Request $request)
    {
        // 📊 Log de acceso a página inicial
        Log::info('🏠 Acceso a página inicial', [
            'ip' => $request->ip(),
            'user_agent' => $request->headers->get('user-agent'),
            'timestamp' => now()
        ]);

        // Limpia el flujo
        $request->session()->forget([
            'dependenciaId',
            'tramites_seleccionados',
            'persona_data',
            'linea_capturada_finalizada'
        ]);

        $dependencias = Dependencia::all();

        // Vista: resources/views/forms/inicio.blade.php
        return view('forms.inicio', ['dependencias' => $dependencias]);
    }

    /**
     * GET/POST /tramite (mostrar formulario de trámites)
     * Si es POST, recibe selección de dependencia desde inicio
     */
    public function showTramite(Request $request)
    {
        // Si es POST, procesar la selección de dependencia
        if ($request->isMethod('post')) {
            $validated = $request->validate([
                'dependenciaId' => 'required|integer|exists:dependencias,id',
            ]);

            // 📊 Log de selección de dependencia
            Log::info('🏛️ Dependencia seleccionada', [
                'dependencia_id' => $validated['dependenciaId'],
                'ip' => $request->ip(),
                'timestamp' => now()
            ]);

            $request->session()->put('dependenciaId', $validated['dependenciaId']);
        }

        $dependenciaId = $request->session()->get('dependenciaId');
        if (!$dependenciaId) {
            // 🚨 Log de intento de acceso sin dependencia
            Log::warning('⚠️ Intento de acceso a trámites sin dependencia', [
                'ip' => $request->ip(),
                'user_agent' => $request->headers->get('user-agent'),
                'timestamp' => now()
            ]);
            
            return redirect()->route('inicio')->with('error', 'Por favor, selecciona una dependencia primero.');
        }

        $dependencia = Dependencia::findOrFail($dependenciaId);
        $tramites = Tramite::where('clave_tramite', 'like', '%' . $dependencia->clave_dependencia . '%')
            ->where('tipo_agrupador', 'P')
            ->get();

        // Vista: resources/views/forms/tramite.blade.php
        return view('forms.tramite', [
            'dependencia' => $dependencia,
            'tramites'    => $tramites,
        ]);
    }

    /**
     * POST /tramite (recibe selección de dependencia desde inicio)
     */
    public function storeDependenciaSelection(Request $request)
    {
        $validated = $request->validate([
            'dependenciaId' => 'required|integer|exists:dependencias,id',
        ]);

        $request->session()->put('dependenciaId', $validated['dependenciaId']);

        return redirect()->route('tramite.show');
    }

    /**
     * POST /persona  (recibe selección de trámites)
     */
    public function storeTramiteSelection(Request $request)
    {
        // 📊 Log mejorado para debugging y seguridad
        Log::info('📋 Selección de trámites iniciada', [
            'ip' => $request->ip(),
            'user_agent' => $request->headers->get('user-agent'),
            'dependencia_id' => $request->session()->get('dependenciaId'),
            'timestamp' => now()
        ]);

        $validated = $request->validate([
            'tramite_ids'   => 'required|array|min:1|max:10',
            'tramite_ids.*' => 'integer|exists:tramites,id',
        ]);

        $request->session()->put('tramites_seleccionados', $validated['tramite_ids']);

        // 📊 Log de trámites guardados con más contexto
        Log::info('✅ Trámites guardados en sesión', [
            'tramites_seleccionados' => $validated['tramite_ids'],
            'cantidad_tramites' => count($validated['tramite_ids']),
            'dependencia_id' => $request->session()->get('dependenciaId'),
            'ip' => $request->ip(),
            'timestamp' => now()
        ]);

        return redirect()->route('persona.show');
    }

    /**
     * GET /persona (form de persona)
     */
    public function showPersonaForm(Request $request)
    {
        if (
            !$request->session()->has('dependenciaId') ||
            !$request->session()->has('tramites_seleccionados')
        ) {
            return redirect()->route('tramite.show')
                ->with('error', 'Por favor, selecciona al menos un trámite primero.');
        }

        // Vista: resources/views/forms/persona.blade.php
        return view('forms.persona');
    }

    /**
     * GET /persona - Maneja recargas de página
     */
    public function showPersonaReload(Request $request)
    {
        // Validar que tenga los datos necesarios en sesión
        if (!$request->session()->has('dependenciaId') || !$request->session()->has('tramites_seleccionados')) {
            return redirect()->route('inicio')->with('error', 'Por favor, inicia el proceso nuevamente.');
        }

        return $this->showPersonaForm($request);
    }

    /**
     * POST /pago (recibe datos de persona y redirige a pago)
     */
    public function storePersonaData(Request $request)
    {
        // 📊 Log de inicio de captura de datos personales
        Log::info('👤 Captura de datos personales iniciada', [
            'tipo_persona' => $request->input('tipo_persona'),
            'ip' => $request->ip(),
            'dependencia_id' => $request->session()->get('dependenciaId'),
            'timestamp' => now()
        ]);

        $tipoPersona = $request->input('tipo_persona');

        $rules = ['tipo_persona' => 'required|in:fisica,moral'];

        if ($tipoPersona === 'fisica') {
            $rules += [
                'curp'             => 'required|string|size:18',
                'rfc'              => 'required|string|size:13',
                'nombres'          => 'required|string|max:60',
                'apellido_paterno' => 'required|string|max:60',
                'apellido_materno' => 'nullable|string|max:60',
            ];
        } else {
            $rules += [
                'rfc_moral'    => 'required|string|size:12',
                'razon_social' => 'required|string|max:120'
            ];
        }

        $validatedData = $request->validate($rules);

        if ($tipoPersona === 'moral') {
            $validatedData['rfc'] = $validatedData['rfc_moral'];
            unset($validatedData['rfc_moral']);
        }

        if ($tipoPersona === 'fisica' && empty($validatedData['apellido_materno'])) {
            $validatedData['apellido_materno'] = null;
        }

        $request->session()->put('persona_data', $validatedData);

        // 📊 Log de datos personales guardados (sin datos sensibles)
        Log::info('✅ Datos personales validados y guardados', [
            'tipo_persona' => $tipoPersona,
            'tiene_rfc' => !empty($validatedData['rfc']),
            'tiene_curp' => !empty($validatedData['curp'] ?? null),
            'ip' => $request->ip(),
            'dependencia_id' => $request->session()->get('dependenciaId'),
            'timestamp' => now()
        ]);

        return redirect()->route('pago.show');
    }

    /**
     * GET /pago
     */
    public function showPagoPage(Request $request)
    {
        $dependenciaId = $request->session()->get('dependenciaId');
        $tramiteIds    = $request->session()->get('tramites_seleccionados');
        $personaData   = $request->session()->get('persona_data');

        if (!$dependenciaId || empty($tramiteIds) || !$personaData) {
            return redirect()->route('inicio')
                ->with('error', 'Tu sesión ha expirado, por favor inicia de nuevo.');
        }

        $dependencia = Dependencia::findOrFail($dependenciaId);
        $tramites    = Tramite::whereIn('id', $tramiteIds)->get();

        // Vista: resources/views/forms/pago.blade.php
        return view('forms.pago', compact('dependencia', 'tramites', 'personaData'));
    }

    /**
     * GET /pago - Maneja recargas de página
     */
    public function showPagoReload(Request $request)
    {
        // Validar que tenga todos los datos necesarios en sesión
        if (!$request->session()->has('dependenciaId') || 
            !$request->session()->has('tramites_seleccionados') || 
            !$request->session()->has('persona_data')) {
            return redirect()->route('inicio')->with('error', 'Por favor, inicia el proceso nuevamente.');
        }

        return $this->showPagoPage($request);
    }

    /**
     * GET /generar-linea - Maneja recargas de página
     */
    public function showLineaCapturada(Request $request)
    {
        // Si hay una línea capturada finalizada en sesión, mostrarla
        if ($request->session()->has('linea_capturada_finalizada')) {
            // Obtener la última línea capturada del usuario (simulamos por IP por ahora)
            $ultimaLinea = LineasCapturadas::latest()->first();
            
            if ($ultimaLinea) {
                $jsonArray = json_decode($ultimaLinea->json_generado, true);
                $respuestaSat = [
                    'exito' => $ultimaLinea->procesado_exitosamente,
                    'datos' => json_decode($ultimaLinea->json_recibido, true),
                    'html_decodificado' => $ultimaLinea->html_codificado ? base64_decode($ultimaLinea->html_codificado) : null
                ];
                
                return view('forms.lineacaptura', [
                    'lineaCapturada' => $ultimaLinea,
                    'jsonParaSat'    => json_encode($jsonArray, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
                    'respuestaSat'   => $respuestaSat
                ]);
            }
        }
        
        // Si no hay línea capturada, redirigir al inicio
        return redirect()->route('inicio')->with('error', 'No hay línea de captura disponible. Por favor, inicia el proceso nuevamente.');
    }

    /**
     * POST /generar-linea
     */
    public function generarLineaCaptura(Request $request)
    {
        // 📊 Log crítico de inicio de generación
        Log::info('🎯 Generación de línea de captura iniciada', [
            'ip' => $request->ip(),
            'user_agent' => $request->headers->get('user-agent'),
            'timestamp' => now()
        ]);

        $dependenciaId = $request->session()->get('dependenciaId');
        $tramiteIds    = $request->session()->get('tramites_seleccionados');
        $personaData   = $request->session()->get('persona_data');

        if (!$dependenciaId || empty($tramiteIds) || !$personaData) {
            // 🚨 Log de intento de generación sin datos completos
            Log::warning('⚠️ Intento de generación sin datos completos', [
                'tiene_dependencia' => !empty($dependenciaId),
                'tiene_tramites' => !empty($tramiteIds),
                'tiene_persona' => !empty($personaData),
                'ip' => $request->ip(),
                'timestamp' => now()
            ]);

            return redirect()->route('inicio')
                ->with('error', 'Tu sesión ha expirado, por favor inicia de nuevo.');
        }

        $dependencia = Dependencia::findOrFail($dependenciaId);
        $tramites    = Tramite::whereIn('id', $tramiteIds)->get();

        $totalCuotas = 0;
        $totalIvas   = 0;

        foreach ($tramites as $tramite) {
            $totalCuotas += $tramite->cuota;
            if ($tramite->iva) {
                $totalIvas += round($tramite->cuota * 0.16, 2);
            }
        }

        // Total redondeado (entero) antes de persistir
        $importeTotalGeneralSinRedondear = $totalCuotas + $totalIvas;
        $importeTotalGeneralRedondeado   = round($importeTotalGeneralSinRedondear);

        $lineaCapturada = LineasCapturadas::create([
            'tipo_persona'      => ($personaData['tipo_persona'] === 'fisica' ? 'F' : 'M'),
            'curp'              => $personaData['curp'] ?? null,
            'rfc'               => $personaData['rfc'],
            'razon_social'      => $personaData['razon_social'] ?? null,
            'nombres'           => $personaData['nombres'] ?? null,
            'apellido_paterno'  => $personaData['apellido_paterno'] ?? null,
            'apellido_materno'  => $personaData['apellido_materno'] ?? null,
            'dependencia_id'    => $dependenciaId,
            'tramite_id'        => implode(',', $tramiteIds),
            'importe_cuota'     => $totalCuotas,
            'importe_iva'       => $totalIvas,
            'importe_total'     => $importeTotalGeneralRedondeado, // guardamos redondeado
            'fecha_vigencia'    => Carbon::now()->addMonth()->toDateString(),
        ]);

        $jsonArray = $this->buildFullJsonForMultiple($lineaCapturada, $dependencia, $tramites);

        $lineaCapturada->solicitud     = $jsonArray['DatosGenerales']['Solicitud'];
        $lineaCapturada->json_generado = json_encode($jsonArray);
        $lineaCapturada->save();

        // ==========================================================
        //  INTEGRACIÓN CON API DEL SAT
        // ==========================================================
        $respuestaSat = $this->enviarJsonASat($jsonArray);
        
        if ($respuestaSat['exito']) {
            // Procesar respuesta exitosa del SAT
            $datosProcesados = $this->procesarRespuestaSat($respuestaSat['datos'], json_encode($respuestaSat['datos']));
            
            // Actualizar el registro con la respuesta del SAT
            $lineaCapturada->update([
                'json_recibido' => json_encode($respuestaSat['datos']),
                'id_documento' => $datosProcesados['id_documento'] ?? null,
                'tipo_pago' => $datosProcesados['tipo_pago'] ?? null,
                'html_codificado' => $datosProcesados['html_codificado'] ?? null,
                'resultado' => $datosProcesados['resultado'] ?? null,
                'linea_captura' => $datosProcesados['linea_captura'] ?? null,
                'importe_sat' => $datosProcesados['importe_sat'] ?? null,
                'fecha_vigencia_sat' => $datosProcesados['fecha_vigencia_sat'] ?? null,
                'errores_sat' => null,
                'fecha_respuesta_sat' => now(),
                'procesado_exitosamente' => true
            ]);
            
            // Agregar HTML decodificado a la respuesta para la vista
            $respuestaSat['html_decodificado'] = $datosProcesados['html_decodificado'] ?? null;
        } else {
            // Guardar errores del SAT
            $lineaCapturada->update([
                'errores_sat' => json_encode(['error' => $respuestaSat['error'] ?? 'Error desconocido']),
                'fecha_respuesta_sat' => now(),
                'procesado_exitosamente' => false
            ]);
        }

        // Reset del flujo y bandera final
        $request->session()->flush();
        $request->session()->put('linea_capturada_finalizada', true);

        // 📊 Log crítico de línea de captura generada exitosamente
        Log::info('🎉 Línea de captura generada exitosamente', [
            'linea_id' => $lineaCapturada->id,
            'solicitud' => $lineaCapturada->solicitud,
            'dependencia_id' => $dependenciaId,
            'cantidad_tramites' => count($tramiteIds),
            'importe_total' => $importeTotalGeneralRedondeado,
            'tipo_persona' => $personaData['tipo_persona'],
            'procesado_exitosamente' => $respuestaSat['exito'] ?? false,
            'ip' => $request->ip(),
            'timestamp' => now()
        ]);

        // Vista: resources/views/forms/lineacaptura.blade.php
        return view('forms.lineacaptura', [
            'lineaCapturada' => $lineaCapturada,
            'jsonParaSat'    => json_encode($jsonArray, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
            'respuestaSat'   => $respuestaSat
        ]);
    }

    // ----------------- PRIVADAS -----------------

    private function buildFullJsonForMultiple(LineasCapturadas $linea, Dependencia $dep, $tramites): array
    {
        $idSolicitud = $dep->clave_dependencia . $dep->unidad_administrativa . date('Y') . str_pad($linea->id, 10, '0', STR_PAD_LEFT);

        return [
            'DatosGenerales' => $this->buildDatosGenerales($idSolicitud, $linea, $dep),
            'Tramites'       => $this->buildTramitesForMultiple($tramites, $linea->importe_total), // total ya redondeado
        ];
    }

    private function buildDatosGenerales(string $idSolicitud, LineasCapturadas $linea, Dependencia $dep): array
    {
        $datos = [
            'Solicitud'          => $idSolicitud,
            'CveDependencia'     => $dep->clave_dependencia,
            'UnidadAdministrativa'=> $dep->unidad_administrativa,
            'TipoPersona'        => $linea->tipo_persona,
            'RFC'                => $linea->rfc,
        ];

        if ($linea->tipo_persona === 'F') {
            $datos['CURP']           = $linea->curp;
            $datos['Nombre']         = $linea->nombres;
            $datos['ApellidoPaterno']= $linea->apellido_paterno;
            $datos['ApellidoMaterno']= $linea->apellido_materno;
        } else {
            $datos['RazonSocial'] = $linea->razon_social;
        }

        $datos['DatosLineaCaptura'] = [
            'FechaSolicitud' => Carbon::parse($linea->created_at)->format('d/m/Y H:i'),
            'Importe'        => $linea->importe_total, // redondeado
            'FechaVigencia'  => Carbon::parse($linea->fecha_vigencia)->format('d/m/Y'),
        ];

        return $datos;
    }

    private function buildTramitesForMultiple($tramites, $totalRedondeado): array
    {
        $tramitesArray = [];
        $numeroSecuenciaGlobal = 1;

        $totalSinRedondear = 0;
        foreach ($tramites as $t) {
            $montoIva = $t->iva ? round($t->cuota * 0.16, 2) : 0;
            $totalSinRedondear += $t->cuota + $montoIva;
        }

        $diferenciaRedondeo = $totalRedondeado - $totalSinRedondear;

        foreach ($tramites as $index => $tramite) {
            $montoIva   = $tramite->iva ? round($tramite->cuota * 0.16, 2) : 0;
            $totalTram  = $tramite->cuota + $montoIva;

            if ($index === count($tramites) - 1) {
                $totalTram += $diferenciaRedondeo; // ajusta al último
            }

            $conceptos = [];
            $conceptos[] = $this->buildConcepto($numeroSecuenciaGlobal++, 'P', $tramite, $tramite->cuota);

            if ($tramite->iva) {
                $montoIvaAjustado = $montoIva;
                if ($index === count($tramites) - 1) {
                    $montoIvaAjustado += $diferenciaRedondeo;
                }
                $conceptos[] = $this->buildConcepto($numeroSecuenciaGlobal++, 'S', $tramite, $montoIvaAjustado, '130009');
            }

            $tramitesArray[] = [
                'NumeroTramite'   => $index + 1,
                'Homoclave'       => $tramite->clave_tramite,
                'Variante'        => $tramite->variante,
                'NumeroConceptos' => count($conceptos),
                'TotalTramite'    => round($totalTram, 2),
                'Conceptos'       => ['Concepto' => $conceptos],
            ];
        }

        return ['Tramite' => $tramitesArray];
    }

    private function buildConcepto(int $secuencia, string $tipoAgrupador, Tramite $tramite, float $monto, string $claveConcepto = null): array
    {
        $monto = round($monto, 2);

        $transacciones = [
            ['ClaveTransaccion' => '4011', 'ValorTransaccion' => $monto],
            ['ClaveTransaccion' => '4243', 'ValorTransaccion' => $monto],
            ['ClaveTransaccion' => '4423', 'ValorTransaccion' => $monto],
        ];

        return [
            'NumeroSecuencia'   => $secuencia,
            'ClaveConcepto'     => $claveConcepto ?? (string) $tramite->clave_contable,
            'Agrupador'         => [
                'IdAgrupador'  => (int) $tramite->agrupador,
                'TipoAgrupador'=> $tipoAgrupador
            ],
            'DatosIcep'         => [
                'ClavePeriodicidad' => $tramite->clave_periodicidad,
                'ClavePeriodo'      => $tramite->clave_periodo,
                'FechaCausacion'    => Carbon::now()->format('d/m/Y')
            ],
            'TotalContribuciones' => $monto,
            'TotalConcepto'       => $monto,
            'DP'                  => ['TransaccionP' => $transacciones],
        ];
    }

    /**
     * POST /regresar
     */
    public function regresar(Request $request)
    {
        $paso_actual = $request->input('paso_actual');

        if ($paso_actual === 'tramite') {
            $request->session()->forget('dependenciaId');
            return redirect()->route('inicio');
        }

        if ($paso_actual === 'persona') {
            $request->session()->forget('tramites_seleccionados');
            return redirect()->route('tramite.show');
        }

        if ($paso_actual === 'pago') {
            $request->session()->forget('persona_data');
            return redirect()->route('persona.show');
        }

        return redirect()->route('inicio');
    }

    /**
     * Función consolidada para validar y decodificar archivos JSON con HTML base64
     * Combina las mejores características de los scripts de verificación
     * 
     * @param string $rutaArchivo Ruta al archivo JSON
     * @return array Resultado de la validación y decodificación
     */
    public function validarYDecodificarJson(string $rutaArchivo): array
    {
        try {
            // Verificar existencia del archivo
            if (!file_exists($rutaArchivo)) {
                return [
                    'exito' => false,
                    'error' => 'El archivo no existe en la ruta especificada',
                    'ruta' => $rutaArchivo
                ];
            }

            // Leer contenido del archivo
            $contenido = file_get_contents($rutaArchivo);
            $tamanoArchivo = strlen($contenido);

            // Información básica del archivo
            $info = [
                'tamano_bytes' => $tamanoArchivo,
                'primeros_100_chars' => substr($contenido, 0, 100),
                'ultimos_100_chars' => substr($contenido, -100)
            ];

            // Limpiar contenido de posibles caracteres problemáticos
            $contenidoLimpio = trim($contenido);

            // Verificar formato JSON básico
            if (!str_ends_with($contenidoLimpio, '}')) {
                return [
                    'exito' => false,
                    'error' => 'El archivo JSON está incompleto - no termina con "}"',
                    'info' => $info,
                    'diagnostico' => [
                        'termina_con_llave' => false,
                        'ultimo_caracter' => substr($contenidoLimpio, -1)
                    ]
                ];
            }

            // Buscar caracteres de control problemáticos
            $caracteresControl = [];
            for ($i = 0; $i < strlen($contenido); $i++) {
                $ascii = ord($contenido[$i]);
                if ($ascii < 32 && $ascii !== 9 && $ascii !== 10 && $ascii !== 13) {
                    $caracteresControl[] = [
                        'posicion' => $i,
                        'ascii' => $ascii,
                        'hex' => dechex($ascii)
                    ];
                }
            }

            // Verificar balance de llaves
            $llaves_abiertas = substr_count($contenido, '{');
            $llaves_cerradas = substr_count($contenido, '}');

            // Intentar decodificar JSON
            $datosJson = json_decode($contenido, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                return [
                    'exito' => false,
                    'error' => 'Error al decodificar JSON: ' . json_last_error_msg(),
                    'info' => $info,
                    'diagnostico' => [
                        'codigo_error_json' => json_last_error(),
                        'caracteres_control' => $caracteresControl,
                        'balance_llaves' => [
                            'abiertas' => $llaves_abiertas,
                            'cerradas' => $llaves_cerradas,
                            'balanceado' => $llaves_abiertas === $llaves_cerradas
                        ]
                    ]
                ];
            }

            // Validar estructura esperada
            $estructura = [
                'tiene_datos_generales' => isset($datosJson['DatosGenerales']),
                'tiene_acuse' => isset($datosJson['Acuse']),
                'tiene_html' => isset($datosJson['Acuse']['HTML'])
            ];

            if (!$estructura['tiene_html']) {
                return [
                    'exito' => false,
                    'error' => 'No se encontró el campo HTML en la estructura JSON',
                    'info' => $info,
                    'estructura' => $estructura,
                    'claves_disponibles' => array_keys($datosJson)
                ];
            }

            // Decodificar HTML base64
            $htmlBase64 = $datosJson['Acuse']['HTML'];
            $htmlDecodificado = html_entity_decode(base64_decode($htmlBase64), ENT_QUOTES | ENT_HTML5, 'UTF-8');

            // Verificar que la decodificación fue exitosa
            if (empty($htmlDecodificado)) {
                return [
                    'exito' => false,
                    'error' => 'Error al decodificar el HTML base64',
                    'info' => $info,
                    'html_info' => [
                        'longitud_base64' => strlen($htmlBase64),
                        'primeros_50_chars' => substr($htmlBase64, 0, 50)
                    ]
                ];
            }

            // Éxito - retornar todos los datos
            return [
                'exito' => true,
                'mensaje' => 'JSON validado y HTML decodificado exitosamente',
                'info' => $info,
                'estructura' => $estructura,
                'datos_json' => $datosJson,
                'html_base64' => $htmlBase64,
                'html_decodificado' => $htmlDecodificado,
                'estadisticas' => [
                    'caracteres_control_encontrados' => count($caracteresControl),
                    'longitud_html_base64' => strlen($htmlBase64),
                    'longitud_html_decodificado' => strlen($htmlDecodificado),
                    'balance_llaves_correcto' => $llaves_abiertas === $llaves_cerradas
                ]
            ];

        } catch (\Exception $e) {
            return [
                'exito' => false,
                'error' => 'Excepción durante el procesamiento: ' . $e->getMessage(),
                'archivo' => $rutaArchivo
            ];
        }
    }

    /**
     * Función auxiliar para corregir automáticamente JSON truncado
     * 
     * @param string $rutaArchivo Ruta al archivo JSON a corregir
     * @return array Resultado de la corrección
     */
    public function corregirJsonTruncado(string $rutaArchivo): array
    {
        try {
            if (!file_exists($rutaArchivo)) {
                return [
                    'exito' => false,
                    'error' => 'El archivo no existe'
                ];
            }

            $contenido = file_get_contents($rutaArchivo);
            $contenidoOriginal = $contenido;
            
            // Limpiar caracteres problemáticos al final
            $contenido = rtrim($contenido, ' "');
            
            // Verificar si necesita corrección
            if ($contenido === $contenidoOriginal) {
                return [
                    'exito' => true,
                    'mensaje' => 'El archivo no necesitaba corrección',
                    'cambios_realizados' => false
                ];
            }

            // Guardar archivo corregido
            file_put_contents($rutaArchivo, $contenido);

            return [
                'exito' => true,
                'mensaje' => 'Archivo JSON corregido exitosamente',
                'cambios_realizados' => true,
                'caracteres_removidos' => strlen($contenidoOriginal) - strlen($contenido)
            ];

        } catch (\Exception $e) {
            return [
                'exito' => false,
                'error' => 'Error al corregir archivo: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Envía el JSON generado a la API del SAT para validación
     * 
     * @param array $jsonData Los datos JSON a enviar al SAT
     * @return array Respuesta de la API del SAT
     */
    private function enviarJsonASat($jsonData)
    {
        // Obtener configuración de la API del SAT desde el archivo .env
        $satApiUrl = env('SAT_API_URL', 'https://api.sat.gob.mx/validacion/linea-captura');
        $satApiToken = env('SAT_API_TOKEN', null);
        $satApiKey = env('SAT_API_KEY', null);
        $timeout = env('SAT_API_TIMEOUT', 30);
        
        // Preparar headers para la petición
        $headers = [
            'Content-Type: application/json',
            'Accept: application/json',
            'User-Agent: LineaCaptura-IMT/1.0'
        ];
        
        // Agregar token de autenticación si está configurado
        if ($satApiToken) {
            $headers[] = 'Authorization: Bearer ' . $satApiToken;
        }
        
        // Agregar API Key si está configurada
        if ($satApiKey) {
            $headers[] = 'X-API-Key: ' . $satApiKey;
        }
        
        // Inicializar cURL
        $curl = curl_init();
        
        curl_setopt_array($curl, [
            CURLOPT_URL => $satApiUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => $timeout,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($jsonData),
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
        ]);
        
        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $error = curl_error($curl);
        
        curl_close($curl);
        
        // Verificar errores de conexión
        if ($error) {
            return [
                'exito' => false,
                'error' => 'Error de conexión con la API del SAT: ' . $error,
                'codigo_http' => 0
            ];
        }
        
        // Verificar código de respuesta HTTP
        if ($httpCode !== 200) {
            return [
                'exito' => false,
                'error' => 'La API del SAT respondió con código HTTP: ' . $httpCode,
                'codigo_http' => $httpCode,
                'respuesta_cruda' => $response
            ];
        }
        
        // Decodificar respuesta JSON
        $responseData = json_decode($response, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            return [
                'exito' => false,
                'error' => 'Error al decodificar la respuesta JSON del SAT: ' . json_last_error_msg(),
                'codigo_http' => $httpCode,
                'respuesta_cruda' => $response
            ];
        }
        
        return [
            'exito' => true,
            'datos' => $responseData,
            'codigo_http' => $httpCode
        ];
    }

    /**
     * Procesa la respuesta recibida del SAT y extrae los datos necesarios
     * 
     * @param array $datosRespuesta Datos JSON decodificados del SAT
     * @param string $respuestaCompleta Respuesta completa en texto
     * @return array Datos procesados y estructurados
     */
    private function procesarRespuestaSat(array $datosRespuesta, string $respuestaCompleta): array
    {
        try {
            // Verificar estructura básica de la respuesta
            if (!isset($datosRespuesta['Acuse'])) {
                return [
                    'exito' => false,
                    'mensaje' => 'La respuesta del SAT no contiene la estructura esperada (falta Acuse)',
                    'errores' => [
                        'estructura_recibida' => array_keys($datosRespuesta),
                        'datos_completos' => $datosRespuesta
                    ]
                ];
            }

            $acuse = $datosRespuesta['Acuse'];
            
            // Extraer HTML codificado
            $htmlCodificado = $acuse['HTML'] ?? null;
            $htmlDecodificado = null;
            
            if ($htmlCodificado) {
                $htmlDecodificado = html_entity_decode(base64_decode($htmlCodificado), ENT_QUOTES | ENT_HTML5, 'UTF-8');
            }

            // Extraer datos principales
            $resultado = [
                'exito' => true,
                'mensaje' => 'Respuesta del SAT procesada exitosamente',
                'json_completo' => $datosRespuesta,
                'id_documento' => $acuse['IdDocumento'] ?? null,
                'tipo_pago' => $acuse['TipoPago'] ?? null,
                'html_codificado' => $htmlCodificado,
                'html_decodificado' => $htmlDecodificado,
                'resultado' => $acuse['Resultado'] ?? null,
                'linea_captura' => $acuse['LineaCaptura'] ?? null,
                'importe_sat' => $acuse['Importe'] ?? null,
                'fecha_vigencia_sat' => $acuse['FechaVigencia'] ?? null,
                'errores' => $acuse['Errores'] ?? null,
                'datos_adicionales' => [
                    'solicitud_id' => $acuse['Solicitud'] ?? null,
                    'fecha_proceso' => $acuse['FechaProceso'] ?? null,
                    'codigo_respuesta' => $acuse['CodigoRespuesta'] ?? null
                ]
            ];

            // Validar que se recibió el HTML
            if (!$htmlCodificado) {
                $resultado['exito'] = false;
                $resultado['mensaje'] = 'La respuesta del SAT no contiene el HTML codificado';
                $resultado['errores'] = ['html_faltante' => 'No se encontró HTML en la respuesta'];
            }

            return $resultado;

        } catch (\Exception $e) {
            return [
                'exito' => false,
                'mensaje' => 'Error al procesar la respuesta del SAT',
                'errores' => [
                    'excepcion' => $e->getMessage(),
                    'datos_recibidos' => $datosRespuesta
                ]
            ];
        }
    }
}
