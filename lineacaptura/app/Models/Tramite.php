<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tramite extends Model
{
    // Definir la tabla asociada
    protected $table = 'tramites';

    // Campos que se pueden asignar masivamente
    protected $fillable = [
        'clave_dependencia_siglas', 'clave_tramite', 'variante', 'descripcion',
        'tramite_usoreservado', 'fundamento_legal', 'vigencia_tramite_de', 
        'vigencia_tramite_al', 'vigencia_lineacaptura', 'tipo_vigencia', 'clave_contable',
        'obligatorio', 'agrupador', 'tipo_agrupador', 'nombre_monto', 'variable', 
        'cuota', 'iva', 'porcentaje_iva', 'actualizacion', 'recargos', 
        'multa_correccionfiscal', 'compensacion', 'saldo_favor'
    ];

    // Relación con Dependencia
    public function dependencia()
    {
        return $this->belongsTo(Dependencia::class, 'clave_dependencia_siglas', 'clave_dependencia');
    }

    // Campos de fechas
    public $timestamps = true;
}
