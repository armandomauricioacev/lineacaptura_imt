<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Persona extends Model
{
    // Definir la tabla asociada si es diferente al nombre del modelo (opcional)
    protected $table = 'personas';

    // Campos que se pueden asignar masivamente
    protected $fillable = [
        'tipo_persona', 'curp', 'rfc', 'razon_social', 
        'nombres', 'apellido_paterno', 'apellido_materno'
    ];

    // Campos de fechas
    public $timestamps = true;  // Laravel usa created_at y updated_at por defecto

    // Agrega relaciones si en el futuro existen relaciones con otras tablas
}
