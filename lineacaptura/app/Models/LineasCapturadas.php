<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LineasCapturadas extends Model
{
    protected $table = 'lineas_capturadas';

    // Definir los campos que pueden ser asignados masivamente
    protected $fillable = [
        'tipo_persona', 'curp', 'rfc', 'razon_social', 'nombres', 'apellido_paterno', 
        'apellido_materno', 'dependencia_id', 'tramite_id', 'estado_pago', 'fecha_solicitud', 'fecha_vigencia'
    ];

    // Relación con la tabla de dependencias
    public function dependencia()
    {
        return $this->belongsTo(Dependencia::class, 'dependencia_id');
    }

    // Relación con la tabla de tramites
    public function tramite()
    {
        return $this->belongsTo(Tramite::class, 'tramite_id');
    }

    // Definir los campos de fecha
    public $timestamps = true;
}
