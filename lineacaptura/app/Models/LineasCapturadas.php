<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LineasCapturadas extends Model
{
    use HasFactory;

    protected $table = 'lineas_capturadas';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'tipo_persona',
        'curp',
        'rfc',
        'razon_social',
        'nombres',
        'apellido_paterno',
        'apellido_materno',
        'dependencia_id',
        'tramite_id',
        'estado_pago',
        'fecha_solicitud',
        'fecha_vigencia',

        // ==========================================================
        //  AÑADIR ESTOS CAMPOS PARA PERMITIR EL GUARDADO MASIVO
        // ==========================================================
        'solicitud',
        'importe_cuota',
        'importe_iva',
        'importe_total',
        'json_generado',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'json_generado' => 'array', // Para que Laravel lo maneje como un array
    ];

    // Relaciones
    public function dependencia()
    {
        return $this->belongsTo(Dependencia::class);
    }

    public function tramite()
    {
        return $this->belongsTo(Tramite::class);
    }
}