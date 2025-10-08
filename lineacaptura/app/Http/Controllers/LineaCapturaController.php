<?php

namespace App\Http\Controllers;

use App\Models\Dependencia;
use App\Models\Tramite;
use App\Models\LineasCapturadas;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LineaCapturaController extends Controller
{
    /**
     * Muestra la página de inicio con la lista de dependencias.
     */
    public function index()
    {
        $dependencias = Dependencia::all();
        return view('inicio', ['dependencias' => $dependencias]);
    }

    /**
     * Maneja la selección de trámite.
     */
    public function showTramite(Request $request)
    {
        if ($request->isMethod('post')) {
            $request->validate(['dependenciaId' => 'required|integer|exists:dependencias,id']);
            $request->session()->put('dependenciaId', $request->input('dependenciaId'));
            return redirect()->route('tramite');
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

    /**
     * Recibe el POST de la selección de trámite y lo guarda en sesión.
     */
    public function storeTramiteSelection(Request $request)
    {
        $validated = $request->validate([
            'tramite_id' => 'required|integer|exists:tramites,id',
        ]);
        $request->session()->put('tramite_id', $validated['tramite_id']);
        return redirect()->route('persona.show');
    }

    /**
     * Muestra el formulario para los datos de la persona.
     */
    public function showPersonaForm(Request $request)
    {
        if (!$request->session()->has('dependenciaId') || !$request->session()->has('tramite_id')) {
            return redirect('/tramite')->with('error', 'Por favor, selecciona un trámite primero.');
        }
        return view('persona');
    }

    /**
     * Valida y guarda los datos de la persona en la sesión.
     */
    public function storePersonaData(Request $request)
    {
        $tipoPersona = $request->input('tipo_persona');
        $rules = [
            'tipo_persona' => 'required|in:fisica,moral',
        ];

        if ($tipoPersona === 'fisica') {
            $rules += [
                'curp' => 'required|string|size:18',
                'rfc' => 'required|string|size:13',
                'nombres' => 'required|string|max:60',
                'apellido_paterno' => 'required|string|max:60',
                'apellido_materno' => 'required|string|max:60',
            ];
        } else { // Persona Moral
            $rules += [
                'rfc_moral' => 'required|string|size:12',
                'razon_social' => 'required|string|max:120',
            ];
        }
        
        $validatedData = $request->validate($rules);
        
        if ($tipoPersona === 'moral') {
            $validatedData['rfc'] = $validatedData['rfc_moral'];
            unset($validatedData['rfc_moral']);
        }
        
        $request->session()->put('persona_data', $validatedData);

        return redirect()->route('pago.show');
    }

    /**
     * Muestra la página de resumen (pago).
     */
    public function showPagoPage(Request $request)
    {
        $dependenciaId = $request->session()->get('dependenciaId');
        $tramiteId = $request->session()->get('tramite_id');
        $personaData = $request->session()->get('persona_data');
        
        if (!$dependenciaId || !$tramiteId || !$personaData) {
            return redirect('/inicio')->with('error', 'Tu sesión ha expirado, por favor inicia de nuevo.');
        }
        
        $dependencia = Dependencia::findOrFail($dependenciaId);
        $tramite = Tramite::findOrFail($tramiteId);
        
        return view('pago', compact('dependencia', 'tramite', 'personaData'));
    }

    /**
     * =========================================================================
     * MÉTODO PRINCIPAL REFACTORIZADO Y MÁS LIMPIO
     * =========================================================================
     * Guarda la información completa en la BD y genera el JSON.
     */
    public function generarLineaCaptura(Request $request)
    {
        // 1. Recuperar datos
        $dependenciaId = $request->session()->get('dependenciaId');
        $tramiteId = $request->session()->get('tramite_id');
        $personaData = $request->session()->get('persona_data');

        if (!$dependenciaId || !$tramiteId || !$personaData) {
            return redirect('/inicio')->with('error', 'Tu sesión ha expirado, por favor inicia de nuevo.');
        }

        // 2. Obtener modelos y calcular montos
        $dependencia = Dependencia::findOrFail($dependenciaId);
        $tramite = Tramite::findOrFail($tramiteId);
        $montoIva = $tramite->iva ? round($tramite->cuota * 0.16, 2) : 0;
        $total = $tramite->cuota + $montoIva;
        
        // 3. Crear el registro en la base de datos
        $lineaCapturada = LineasCapturadas::create([
            'tipo_persona'      => ($personaData['tipo_persona'] === 'fisica' ? 'F' : 'M'),
            'curp'              => $personaData['curp'] ?? null,
            'rfc'               => $personaData['rfc'],
            'razon_social'      => $personaData['razon_social'] ?? null,
            'nombres'           => $personaData['nombres'] ?? null,
            'apellido_paterno'  => $personaData['apellido_paterno'] ?? null,
            'apellido_materno'  => $personaData['apellido_materno'] ?? null,
            'dependencia_id'    => $dependenciaId,
            'tramite_id'        => $tramiteId,
            'importe_cuota'     => $tramite->cuota,
            'importe_iva'       => $montoIva,
            'importe_total'     => $total,
            'fecha_vigencia'    => Carbon::now()->addMonth()->toDateString(),
        ]);

        // 4. Construir el JSON usando las nuevas funciones privadas
        $jsonArray = $this->buildFullJson($lineaCapturada, $dependencia, $tramite);
        
        // 5. Actualizar el registro con los datos finales (solicitud y JSON)
        $lineaCapturada->solicitud = $jsonArray['DatosGenerales']['Solicitud'];
        $lineaCapturada->json_generado = json_encode($jsonArray);
        $lineaCapturada->save();

        // 6. Limpiar la sesión y mostrar la vista final
        $request->session()->flush();
        return view('lineacaptura', [
            'lineaCapturada' => $lineaCapturada,
            'jsonParaSat' => json_encode($jsonArray, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
        ]);
    }

    /**
     * =========================================================================
     * FUNCIONES PRIVADAS PARA CONSTRUIR EL JSON DE FORMA ORDENADA
     * =========================================================================
     */

    /**
     * Construye el objeto JSON completo para el SAT.
     *
     * @param LineasCapturadas $lineaCapturada
     * @param Dependencia $dependencia
     * @param Tramite $tramite
     * @return array
     */
    private function buildFullJson(LineasCapturadas $lineaCapturada, Dependencia $dependencia, Tramite $tramite): array
    {
        $idSolicitud = $dependencia->clave_dependencia . $dependencia->unidad_administrativa . date('Y') . str_pad($lineaCapturada->id, 10, '0', STR_PAD_LEFT);

        return [
            'DatosGenerales' => $this->buildDatosGenerales($idSolicitud, $lineaCapturada, $dependencia),
            'Tramites' => $this->buildTramites($lineaCapturada, $tramite)
        ];
    }

    /**
     * Construye el bloque "DatosGenerales" del JSON de forma dinámica.
     */
    private function buildDatosGenerales(string $idSolicitud, LineasCapturadas $linea, Dependencia $dep): array
    {
        // Base con datos comunes para ambos tipos de persona
        $datos = [
            'Solicitud' => $idSolicitud,
            'CveDependencia' => $dep->clave_dependencia,
            'UnidadAdministrativa' => $dep->unidad_administrativa,
            'TipoPersona' => $linea->tipo_persona,
            'RFC' => $linea->rfc,
        ];

        // Añadir campos específicos según el tipo de persona
        if ($linea->tipo_persona === 'F') {
            $datos['CURP'] = $linea->curp;
            $datos['Nombre'] = $linea->nombres;
            $datos['ApellidoPaterno'] = $linea->apellido_paterno;
            $datos['ApellidoMaterno'] = $linea->apellido_materno;
        } else { // 'M'
            $datos['RazonSocial'] = $linea->razon_social;
        }
        
        // Añadir el bloque final de DatosLineaCaptura
        $datos['DatosLineaCaptura'] = [
            'FechaSolicitud' => Carbon::parse($linea->created_at)->format('d/m/Y H:i'),
            'Importe' => $linea->importe_total,
            'FechaVigencia' => Carbon::parse($linea->fecha_vigencia)->format('d/m/Y'),
        ];

        return $datos;
    }

    /**
     * Construye el bloque "Tramites" del JSON.
     */
    private function buildTramites(LineasCapturadas $linea, Tramite $tramite): array
    {
        $conceptos = [];
        $conceptos[] = $this->buildConcepto(1, 'P', $tramite, $linea->importe_cuota);

        if ($tramite->iva) {
            $conceptos[] = $this->buildConcepto(2, 'S', $tramite, $linea->importe_iva, '130009');
        }

        return [
            'Tramite' => [[
                'NumeroTramite' => 1,
                'Homoclave' => $tramite->clave_tramite,
                'Variante' => $tramite->variante,
                'NumeroConceptos' => count($conceptos),
                'TotalTramite' => $linea->importe_total,
                'Conceptos' => ['Concepto' => $conceptos]
            ]]
        ];
    }

    /**
     * Construye un bloque "Concepto" individual, incluyendo sus transacciones.
     */
    private function buildConcepto(int $secuencia, string $tipoAgrupador, Tramite $tramite, float $monto, string $claveConcepto = null): array
    {
        // Construye el bloque de transacciones dinámicamente
        $transacciones = [];
        // Aquí asumimos que las claves de transacción están quemadas, 
        // pero esto se podría hacer dinámico con una tabla de relación.
        $clavesTransaccion = ['4011', '4243', '4423']; 
        foreach ($clavesTransaccion as $clave) {
            $transacciones[] = [
                'ClaveTransaccion' => $clave,
                'ValorTransaccion' => $monto
            ];
        }

        return [
            'NumeroSecuencia' => $secuencia,
            'ClaveConcepto' => $claveConcepto ?? (string)$tramite->clave_contable,
            'Agrupador' => [
                'IdAgrupador' => (int)$tramite->agrupador,
                'TipoAgrupador' => $tipoAgrupador
            ],
            'DatosIcep' => [
                'ClavePeriodicidad' => $tramite->clave_periodicidad,
                'ClavePeriodo' => $tramite->clave_periodo,
                'FechaCausacion' => Carbon::now()->format('d/m/Y')
            ],
            'TotalContribuciones' => $monto,
            'TotalConcepto' => $monto,
            'DP' => [
                'TransaccionP' => $transacciones
            ]
        ];
    }
}