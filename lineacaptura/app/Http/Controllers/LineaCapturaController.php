<?php

namespace App\Http\Controllers;

use App\Models\Dependencia;
use App\Models\Tramite;
use Illuminate\Http\Request;

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
     * Recibe el POST de la selección de trámite, lo valida,
     * lo guarda en sesión y redirige a la ruta GET para mostrar el formulario.
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
     * Esta ruta ahora es accesible por GET.
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
        
        // ✅ LÓGICA CLAVE PARA UNIFICAR EL RFC
        // Si la persona es moral, creamos la clave 'rfc' a partir de 'rfc_moral'
        // y luego eliminamos la clave original para mantener los datos limpios.
        if ($tipoPersona === 'moral') {
            $validatedData['rfc'] = $validatedData['rfc_moral'];
            unset($validatedData['rfc_moral']);
        }
        
        // Guardamos todos los datos de la persona en la sesión
        $request->session()->put('persona_data', $validatedData);

        // Redirigimos a la ruta que muestra la página de pago
        return redirect()->route('pago.show');
    }

    /**
     * Muestra la página de resumen (pago) con toda la información recopilada.
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
}