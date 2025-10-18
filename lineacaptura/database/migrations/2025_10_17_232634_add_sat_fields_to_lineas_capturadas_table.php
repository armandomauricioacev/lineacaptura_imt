<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('lineas_capturadas', function (Blueprint $table) {
            // Campos adicionales del SAT
            $table->json('json_recibido')->nullable()->comment('JSON completo recibido del SAT');
            $table->string('id_documento', 10)->nullable()->comment('ID del documento del SAT (6 dígitos numéricos)');
            $table->integer('tipo_pago')->nullable()->comment('Tipo de pago del SAT (1 carácter numérico)');
            $table->longText('html_codificado')->nullable()->comment('HTML codificado recibido del SAT');
            $table->integer('resultado')->nullable()->comment('Resultado de la operación del SAT');
            $table->string('linea_captura', 50)->nullable()->comment('Línea de captura generada por el SAT');
            $table->decimal('importe_sat', 10, 2)->nullable()->comment('Importe confirmado por el SAT');
            $table->date('fecha_vigencia_sat')->nullable()->comment('Fecha de vigencia confirmada por el SAT');
            $table->json('errores_sat')->nullable()->comment('Errores reportados por el SAT');
            $table->timestamp('fecha_respuesta_sat')->nullable()->comment('Fecha y hora de respuesta del SAT');
            $table->boolean('procesado_exitosamente')->default(false)->comment('Indica si la respuesta del SAT fue procesada exitosamente');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lineas_capturadas', function (Blueprint $table) {
            $table->dropColumn([
                'json_recibido',
                'id_documento',
                'tipo_pago',
                'html_codificado',
                'resultado',
                'linea_captura',
                'importe_sat',
                'fecha_vigencia_sat',
                'errores_sat',
                'fecha_respuesta_sat',
                'procesado_exitosamente'
            ]);
        });
    }
};
