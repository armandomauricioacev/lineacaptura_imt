<?php

namespace App\Http\Controllers;

use App\Models\Dependencia;
use App\Models\Tramite;
use App\Models\LineasCapturadas;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LineaCapturaController extends Controller
{
    // ... (Los métodos index, showTramite, storeTramiteSelection, showPersonaForm, storePersonaData y showPagoPage no necesitan cambios) ...

    public function index(Request $request)
    {
        $request->session()->forget(['dependenciaId', 'tramites_seleccionados', 'persona_data', 'linea_capturada_finalizada']);
        $dependencias = Dependencia::all();
        return view('inicio', ['dependencias' => $dependencias]);
    }

    public function showTramite(Request $request)
    {
        if ($request->isMethod('post')) {
            $request->validate(['dependenciaId' => 'required|integer|exists:dependencias,id']);
            $request->session()->put('dependenciaId', $request->input('dependenciaId'));
            return redirect()->route('tramite.show');
        }
        $dependenciaId = $request->session()->get('dependenciaId');
        if (!$dependenciaId) {
            return redirect('/inicio');
        }
        $dependencia = Dependencia::findOrFail($dependenciaId);
        $tramites = Tramite::where('clave_tramite', 'like', '%' . $dependencia->clave_dependencia . '%')
                             ->where('tipo_agrupador', 'P')
                             ->get();
        return view('tramite', [
            'dependencia' => $dependencia,
            'tramites' => $tramites,
        ]);
    }

    public function storeTramiteSelection(Request $request)
    {
        $validated = $request->validate([
            'tramite_ids'   => 'required|array|min:1|max:10',
            'tramite_ids.*' => 'integer|exists:tramites,id',
        ]);
        $request->session()->put('tramites_seleccionados', $validated['tramite_ids']);
        return redirect()->route('persona.show');
    }

    public function showPersonaForm(Request $request)
    {
        if (!$request->session()->has('dependenciaId') || !$request->session()->has('tramites_seleccionados')) {
            return redirect()->route('tramite.show')->with('error', 'Por favor, selecciona al menos un trámite primero.');
        }
        return view('persona');
    }
    
    public function storePersonaData(Request $request)
    {
        $tipoPersona = $request->input('tipo_persona');
        $rules = [ 'tipo_persona' => 'required|in:fisica,moral' ];
        if ($tipoPersona === 'fisica') {
            $rules += [
                'curp' => 'required|string|size:18', 'rfc' => 'required|string|size:13',
                'nombres' => 'required|string|max:60', 'apellido_paterno' => 'required|string|max:60',
                'apellido_materno' => 'nullable|string|max:60',
            ];
        } else {
            $rules += [ 'rfc_moral' => 'required|string|size:12', 'razon_social' => 'required|string|max:120' ];
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
        return redirect()->route('pago.show');
    }

    public function showPagoPage(Request $request)
    {
        $dependenciaId = $request->session()->get('dependenciaId');
        $tramiteIds = $request->session()->get('tramites_seleccionados');
        $personaData = $request->session()->get('persona_data');
        if (!$dependenciaId || empty($tramiteIds) || !$personaData) {
            return redirect('/inicio')->with('error', 'Tu sesión ha expirado, por favor inicia de nuevo.');
        }
        $dependencia = Dependencia::findOrFail($dependenciaId);
        $tramites = Tramite::whereIn('id', $tramiteIds)->get();
        return view('pago', compact('dependencia', 'tramites', 'personaData'));
    }

    public function generarLineaCaptura(Request $request)
    {
        $dependenciaId = $request->session()->get('dependenciaId');
        $tramiteIds = $request->session()->get('tramites_seleccionados');
        $personaData = $request->session()->get('persona_data');

        if (!$dependenciaId || empty($tramiteIds) || !$personaData) {
            return redirect('/inicio')->with('error', 'Tu sesión ha expirado, por favor inicia de nuevo.');
        }

        $dependencia = Dependencia::findOrFail($dependenciaId);
        $tramites = Tramite::whereIn('id', $tramiteIds)->get();

        $totalCuotas = 0;
        $totalIvas = 0;
        foreach ($tramites as $tramite) {
            $totalCuotas += $tramite->cuota;
            if ($tramite->iva) {
                $totalIvas += round($tramite->cuota * 0.16, 2);
            }
        }
        
        // ========================================================== //
        // INICIO DE LA CORRECCIÓN                                    //
        // Se redondea el importe total general antes de usarlo.      //
        // ========================================================== //
        $importeTotalGeneralSinRedondear = $totalCuotas + $totalIvas;
        $importeTotalGeneralRedondeado = round($importeTotalGeneralSinRedondear);
        
        $lineaCapturada = LineasCapturadas::create([
            'tipo_persona' => ($personaData['tipo_persona'] === 'fisica' ? 'F' : 'M'),
            'curp' => $personaData['curp'] ?? null, 'rfc' => $personaData['rfc'],
            'razon_social' => $personaData['razon_social'] ?? null, 'nombres' => $personaData['nombres'] ?? null,
            'apellido_paterno' => $personaData['apellido_paterno'] ?? null, 'apellido_materno' => $personaData['apellido_materno'] ?? null,
            'dependencia_id' => $dependenciaId, 'tramite_id' => implode(',', $tramiteIds),
            'importe_cuota' => $totalCuotas, 
            'importe_iva' => $totalIvas,
            // Se guarda el importe ya redondeado en la base de datos
            'importe_total' => $importeTotalGeneralRedondeado, 
            'fecha_vigencia' => Carbon::now()->addMonth()->toDateString(),
        ]);

        // La función que construye el JSON ahora usará el valor redondeado de $lineaCapturada->importe_total
        $jsonArray = $this->buildFullJsonForMultiple($lineaCapturada, $dependencia, $tramites);
        
        $lineaCapturada->solicitud = $jsonArray['DatosGenerales']['Solicitud'];
        $lineaCapturada->json_generado = json_encode($jsonArray);
        $lineaCapturada->save();

        $request->session()->flush();
        $request->session()->put('linea_capturada_finalizada', true);

        return view('lineacaptura', [
            'lineaCapturada' => $lineaCapturada,
            'jsonParaSat' => json_encode($jsonArray, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
        ]);
    }
    
    // --- FUNCIONES PRIVADAS ---
    
    private function buildFullJsonForMultiple(LineasCapturadas $linea, Dependencia $dep, $tramites): array
    {
        $idSolicitud = $dep->clave_dependencia . $dep->unidad_administrativa . date('Y') . str_pad($linea->id, 10, '0', STR_PAD_LEFT);
        return [
            'DatosGenerales' => $this->buildDatosGenerales($idSolicitud, $linea, $dep),
            'Tramites' => $this->buildTramitesForMultiple($tramites, $linea->importe_total) // Pasamos el total redondeado
        ];
    }

    private function buildDatosGenerales(string $idSolicitud, LineasCapturadas $linea, Dependencia $dep): array
    {
        $datos = [ 'Solicitud' => $idSolicitud, 'CveDependencia' => $dep->clave_dependencia, 'UnidadAdministrativa' => $dep->unidad_administrativa, 'TipoPersona' => $linea->tipo_persona, 'RFC' => $linea->rfc ];
        if ($linea->tipo_persona === 'F') {
            $datos['CURP'] = $linea->curp; $datos['Nombre'] = $linea->nombres; $datos['ApellidoPaterno'] = $linea->apellido_paterno; $datos['ApellidoMaterno'] = $linea->apellido_materno;
        } else { $datos['RazonSocial'] = $linea->razon_social; }
        
        // ========================================================== //
        // INICIO DE LA CORRECCIÓN                                    //
        // El JSON se construye con el 'importe_total' ya redondeado. //
        // ========================================================== //
        $datos['DatosLineaCaptura'] = [ 
            'FechaSolicitud' => Carbon::parse($linea->created_at)->format('d/m/Y H:i'), 
            'Importe' => $linea->importe_total, // Este valor ya está redondeado desde la creación del modelo
            'FechaVigencia' => Carbon::parse($linea->fecha_vigencia)->format('d/m/Y') 
        ];
        return $datos;
    }

    private function buildTramitesForMultiple($tramites, $totalRedondeado): array
    {
        $tramitesArray = [];
        $numeroSecuenciaGlobal = 1;
        
        // Calculamos la suma de los totales individuales sin redondear
        $totalSinRedondear = 0;
        foreach ($tramites as $tramite) {
            $montoIva = $tramite->iva ? round($tramite->cuota * 0.16, 2) : 0;
            $totalSinRedondear += $tramite->cuota + $montoIva;
        }
        
        // Obtenemos la diferencia por el redondeo
        $diferenciaRedondeo = $totalRedondeado - $totalSinRedondear;

        foreach ($tramites as $index => $tramite) {
            $montoIva = $tramite->iva ? round($tramite->cuota * 0.16, 2) : 0;
            $totalTramite = $tramite->cuota + $montoIva;
            
            // Al último trámite le sumamos la diferencia del redondeo para que el total cuadre
            if ($index === count($tramites) - 1) {
                $totalTramite += $diferenciaRedondeo;
            }

            $conceptos = [];
            $conceptos[] = $this->buildConcepto($numeroSecuenciaGlobal++, 'P', $tramite, $tramite->cuota);
            if ($tramite->iva) {
                $montoIvaAjustado = $montoIva;
                // Si es el último trámite, ajustamos el IVA también si es necesario
                if ($index === count($tramites) - 1) {
                    $montoIvaAjustado += $diferenciaRedondeo;
                }
                $conceptos[] = $this->buildConcepto($numeroSecuenciaGlobal++, 'S', $tramite, $montoIvaAjustado, '130009');
            }
            
            $tramitesArray[] = [
                'NumeroTramite' => $index + 1, 'Homoclave' => $tramite->clave_tramite, 'Variante' => $tramite->variante,
                'NumeroConceptos' => count($conceptos), 
                // Aseguramos que el total del trámite refleje el ajuste
                'TotalTramite' => round($totalTramite, 2), 
                'Conceptos' => ['Concepto' => $conceptos]
            ];
        }
        return ['Tramite' => $tramitesArray];
    }
    
    private function buildConcepto(int $secuencia, string $tipoAgrupador, Tramite $tramite, float $monto, string $claveConcepto = null): array
    {
        // Redondeamos el monto a 2 decimales para asegurar consistencia
        $monto = round($monto, 2);
        
        $transacciones = [
            ['ClaveTransaccion' => '4011', 'ValorTransaccion' => $monto],
            ['ClaveTransaccion' => '4243', 'ValorTransaccion' => $monto],
            ['ClaveTransaccion' => '4423', 'ValorTransaccion' => $monto]
        ];

        return [
            'NumeroSecuencia' => $secuencia, 'ClaveConcepto' => $claveConcepto ?? (string)$tramite->clave_contable,
            'Agrupador' => [ 'IdAgrupador' => (int)$tramite->agrupador, 'TipoAgrupador' => $tipoAgrupador ],
            'DatosIcep' => [ 'ClavePeriodicidad' => $tramite->clave_periodicidad, 'ClavePeriodo' => $tramite->clave_periodo, 'FechaCausacion' => Carbon::now()->format('d/m/Y') ],
            'TotalContribuciones' => $monto, 'TotalConcepto' => $monto,
            'DP' => ['TransaccionP' => $transacciones]
        ];
    }

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
}