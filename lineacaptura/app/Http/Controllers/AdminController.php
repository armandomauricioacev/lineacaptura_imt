<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Dependencia;
use App\Models\Tramite;
use App\Models\LineasCapturadas;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    public function index()
    {
        // Carga datasets. Si alguno crece mucho, considera ->paginate()
        $dependencias     = Dependencia::all();
        $tramites         = Tramite::all();
        $lineasCapturadas = LineasCapturadas::all();
        $users            = User::all();

        // AHORA apunta a resources/views/admin/admin.blade.php
        return view('admin.admin', compact('dependencias', 'tramites', 'lineasCapturadas', 'users'));
    }

    // ========== DEPENDENCIAS ==========
    public function storeDependencia(Request $request)
    {
        $request->validate([
            'nombre'               => 'required|string|max:100',
            'clave_dependencia'    => 'required|string|max:3',
            'unidad_administrativa'=> 'required|string|max:3',
        ]);

        Dependencia::create($request->all());

        return redirect()->route('admin.panel')->with('success', 'Dependencia creada exitosamente');
    }

    public function updateDependencia(Request $request, $id)
    {
        $dependencia = Dependencia::findOrFail($id);

        $request->validate([
            'nombre'               => 'required|string|max:100',
            'clave_dependencia'    => 'required|string|max:3',
            'unidad_administrativa'=> 'required|string|max:3',
        ]);

        $dependencia->update($request->all());

        return redirect()->route('admin.panel')->with('success', 'Dependencia actualizada exitosamente');
    }

    public function destroyDependencia($id)
    {
        $dependencia = Dependencia::findOrFail($id);
        $dependencia->delete();

        return redirect()->route('admin.panel')->with('success', 'Dependencia eliminada exitosamente');
    }

    // ========== TRÁMITES ==========
    public function storeTramite(Request $request)
    {
        $request->validate([
            'clave_dependencia_siglas' => 'required|string|max:10',
            'clave_tramite'            => 'required|string|max:30',
            'variante'                 => 'required|string|max:2',
            'descripcion'              => 'required|string|max:200',
            'tramite_usoreservado'     => 'nullable|string|max:1',
            'fundamento_legal'         => 'nullable|string|max:200',
            'vigencia_tramite_de'      => 'nullable|date',
            'vigencia_tramite_al'      => 'nullable|date',
            'vigencia_lineacaptura'    => 'nullable|integer',
            'tipo_vigencia'            => 'nullable|string|max:1',
            'clave_contable'           => 'nullable|integer',
            'obligatorio'              => 'nullable|string|max:1',
            'agrupador'                => 'nullable|string|max:1',
            'tipo_agrupador'           => 'nullable|string|max:1',
            'clave_periodicidad'       => 'required|string|max:1',
            'clave_periodo'            => 'required|string|max:3',
            'nombre_monto'             => 'nullable|string|max:100',
            'variable'                 => 'nullable|string|max:1',
            'cuota'                    => 'required|numeric|min:0',
            'iva'                      => 'nullable|boolean',
            'actualizacion'            => 'nullable|string|max:1',
            'recargos'                 => 'nullable|string|max:1',
            'multa_correccionfiscal'   => 'nullable|string|max:1',
            'compensacion'             => 'nullable|string|max:1',
            'saldo_favor'              => 'nullable|string|max:1',
        ]);

        $data       = $request->all();
        $data['iva']= $request->has('iva') ? 1 : 0;
        $data['monto_iva'] = $data['iva'] ? ($request->cuota * 0.16) : 0;

        Tramite::create($data);

        return redirect()->route('admin.panel')->with('success', 'Trámite creado exitosamente');
    }

    public function updateTramite(Request $request, $id)
    {
        $tramite = Tramite::findOrFail($id);

        $request->validate([
            'clave_dependencia_siglas' => 'required|string|max:10',
            'clave_tramite'            => 'required|string|max:30',
            'variante'                 => 'required|string|max:2',
            'descripcion'              => 'required|string|max:200',
            'tramite_usoreservado'     => 'nullable|string|max:1',
            'fundamento_legal'         => 'nullable|string|max:200',
            'vigencia_tramite_de'      => 'nullable|date',
            'vigencia_tramite_al'      => 'nullable|date',
            'vigencia_lineacaptura'    => 'nullable|integer',
            'tipo_vigencia'            => 'nullable|string|max:1',
            'clave_contable'           => 'nullable|integer',
            'obligatorio'              => 'nullable|string|max:1',
            'agrupador'                => 'nullable|string|max:1',
            'tipo_agrupador'           => 'nullable|string|max:1',
            'clave_periodicidad'       => 'required|string|max:1',
            'clave_periodo'            => 'required|string|max:3',
            'nombre_monto'             => 'nullable|string|max:100',
            'variable'                 => 'nullable|string|max:1',
            'cuota'                    => 'required|numeric|min:0',
            'iva'                      => 'nullable|boolean',
            'actualizacion'            => 'nullable|string|max:1',
            'recargos'                 => 'nullable|string|max:1',
            'multa_correccionfiscal'   => 'nullable|string|max:1',
            'compensacion'             => 'nullable|string|max:1',
            'saldo_favor'              => 'nullable|string|max:1',
        ]);

        $data       = $request->all();
        $data['iva']= $request->has('iva') ? 1 : 0;
        $data['monto_iva'] = $data['iva'] ? ($request->cuota * 0.16) : 0;

        $tramite->update($data);

        return redirect()->route('admin.panel')->with('success', 'Trámite actualizado exitosamente');
    }

    public function destroyTramite($id)
    {
        $tramite = Tramite::findOrFail($id);
        $tramite->delete();

        return redirect()->route('admin.panel')->with('success', 'Trámite eliminado exitosamente');
    }

    // ========== LÍNEAS DE CAPTURA ==========
    public function updateLineaCaptura(Request $request, $id)
    {
        $linea = LineasCapturadas::findOrFail($id);

        $request->validate([
            'estado_pago'    => 'required|string|max:10',
            'fecha_vigencia' => 'nullable|date',
        ]);

        $linea->update($request->only(['estado_pago', 'fecha_vigencia']));

        return redirect()->route('admin.panel')->with('success', 'Línea de captura actualizada exitosamente');
    }

    public function destroyLineaCaptura($id)
    {
        $linea = LineasCapturadas::findOrFail($id);
        $linea->delete();

        return redirect()->route('admin.panel')->with('success', 'Línea de captura eliminada exitosamente');
    }

    // ========== USUARIOS ==========
    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $data = $request->only(['name', 'email']);
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('admin.panel')->with('success', 'Usuario actualizado exitosamente');
    }

    public function destroyUser($id)
    {
        $user = User::findOrFail($id);

        if (auth()->id() === $user->id) {
            return redirect()->route('admin.panel')->with('error', 'No puedes eliminar tu propia cuenta');
        }

        $user->delete();

        return redirect()->route('admin.panel')->with('success', 'Usuario eliminado exitosamente');
    }
}
