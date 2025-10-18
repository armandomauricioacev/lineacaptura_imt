@extends('layouts.base')

@section('title', 'Línea de Captura Generada')

@section('content')
<style>
    .caja { 
        border:1px solid #e5e5e5; 
        border-radius:6px; 
        padding:20px; 
        background:#fff; 
        margin-top: 20px;
    }
    .json-viewer {
        background-color: #2d2d2d;
        color: #cccccc;
        padding: 20px;
        border-radius: 5px;
        overflow-x: auto;
        white-space: pre;
        font-family: monospace;
    }
    .alert-success {
        color: #3c763d;
        background-color: #dff0d8;
        border-color: #d6e9c6;
        padding: 15px;
        margin-bottom: 20px;
        border: 1px solid transparent;
        border-radius: 4px;
    }
    .html-decoded {
        border: 1px solid #ddd;
        padding: 20px;
        background-color: #f9f9f9;
        border-radius: 5px;
        margin-top: 10px;
    }
    .codigo-original {
        background-color: #f8f9fa;
        border: 1px solid #e9ecef;
        padding: 15px;
        border-radius: 5px;
        font-family: monospace;
        font-size: 12px;
        word-break: break-all;
        max-height: 200px;
        overflow-y: auto;
    }
</style>

<?php
/**
 * Función para decodificar HTML base64 con soporte para caracteres especiales en español
 * Implementación basada en validador_linea_de_captura.php
 */
function decodificarHtmlBase64($codigoBase64) {
    try {
        // Usar la misma implementación que en validador_linea_de_captura.php
        $htmlDecodificado = html_entity_decode(base64_decode($codigoBase64), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        
        // Verificar si la decodificación fue exitosa
        if ($htmlDecodificado === false || empty($htmlDecodificado)) {
            return "Error: No se pudo decodificar el código base64.";
        }
        
        return $htmlDecodificado;
        
    } catch (Exception $e) {
        return "Error al decodificar: " . $e->getMessage();
    }
}

/**
 * Función para obtener y decodificar el contenido del archivo codigo.json
 */
function obtenerYDecodificarCodigo() {
    try {
        $rutaArchivo = resource_path('views/forms/codigo.json');
        
        if (!file_exists($rutaArchivo)) {
            return [
                'error' => true,
                'mensaje' => 'El archivo codigo.json no existe en la ruta especificada.'
            ];
        }
        
        $contenidoArchivo = file_get_contents($rutaArchivo);
        
        // Limpiar el contenido de posibles caracteres problemáticos
        $contenidoArchivo = trim($contenidoArchivo);
        
        // Verificar si el JSON está completo
        if (!str_ends_with($contenidoArchivo, '}')) {
            return [
                'error' => true,
                'mensaje' => 'El archivo JSON está incompleto o truncado. Debe terminar con "}".',
                'debug_info' => [
                    'tamaño_archivo' => strlen($contenidoArchivo),
                    'ultimos_50_caracteres' => substr($contenidoArchivo, -50)
                ]
            ];
        }
        
        $datosJson = json_decode($contenidoArchivo, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            return [
                'error' => true,
                'mensaje' => 'Error al decodificar el JSON: ' . json_last_error_msg(),
                'debug_info' => [
                    'codigo_error' => json_last_error(),
                    'tamaño_archivo' => strlen($contenidoArchivo),
                    'primeros_100_caracteres' => substr($contenidoArchivo, 0, 100),
                    'ultimos_100_caracteres' => substr($contenidoArchivo, -100)
                ]
            ];
        }
        
        if (!isset($datosJson['Acuse']['HTML'])) {
            return [
                'error' => true,
                'mensaje' => 'No se encontró el campo HTML en el JSON.',
                'debug_info' => [
                    'claves_disponibles' => array_keys($datosJson),
                    'estructura_acuse' => isset($datosJson['Acuse']) ? array_keys($datosJson['Acuse']) : 'No existe'
                ]
            ];
        }
        
        $codigoBase64 = $datosJson['Acuse']['HTML'];
        $htmlDecodificado = decodificarHtmlBase64($codigoBase64);
        
        return [
            'error' => false,
            'datos_originales' => $datosJson,
            'codigo_base64' => $codigoBase64,
            'html_decodificado' => $htmlDecodificado
        ];
        
    } catch (Exception $e) {
        return [
            'error' => true,
            'mensaje' => 'Error al procesar el archivo: ' . $e->getMessage()
        ];
    }
}

// Obtener y decodificar el código
$resultadoDecodificacion = obtenerYDecodificarCodigo();
?>

<div class="alert alert-success">
    <strong>¡Éxito!</strong> La información ha sido guardada en la base de datos con el ID: {{ $lineaCapturada->id }}
</div>

<div class="caja">
    <h4>JSON Estático (simulación de envío al SAT)</h4>
    <p>Esta es la estructura del JSON que se enviaría al Web Service del SAT basado en la información proporcionada.</p>
    <hr>
    <div class="json-viewer"><code>{{ $jsonParaSat }}</code></div>
</div>

<!-- ==========================================================
     SECCIÓN: JSON ENVIADO AL SAT
     ========================================================== -->
<div class="caja">
    <h3 style="color: #2563eb; margin-bottom: 15px; font-size: 18px; font-weight: bold;">
        📤 JSON Enviado al SAT
    </h3>
    <div class="json-viewer">
        <pre style="background: #f8fafc; padding: 15px; border-radius: 8px; border: 1px solid #e2e8f0; font-size: 12px; line-height: 1.4; overflow-x: auto; white-space: pre-wrap;">{{ $jsonParaSat }}</pre>
    </div>
</div>

<!-- ==========================================================
     SECCIÓN: RESPUESTA DEL SAT
     ========================================================== -->
<div class="caja">
    <h3 style="color: #059669; margin-bottom: 15px; font-size: 18px; font-weight: bold;">
        📥 Respuesta del SAT
    </h3>
    
    @if(isset($respuestaSat))
        @if($respuestaSat['exito'])
            <div class="alert-success">
                <strong>✅ Conexión exitosa con la API del SAT</strong>
                <p>El JSON fue enviado correctamente y se recibió una respuesta.</p>
            </div>
            
            <!-- Datos de respuesta del SAT -->
            <div style="margin-top: 20px;">
                <h4 style="color: #374151; margin-bottom: 10px;">📋 Datos de la Respuesta:</h4>
                <div style="background: #f0f9ff; padding: 15px; border-radius: 8px; border: 1px solid #0ea5e9;">
                    <p><strong>Código HTTP:</strong> {{ $respuestaSat['codigo_http'] ?? 'N/A' }}</p>
                    <p><strong>Estado:</strong> Procesado exitosamente</p>
                    @if(isset($respuestaSat['datos']))
                        <p><strong>Datos recibidos:</strong> Sí ({{ count($respuestaSat['datos']) }} campos)</p>
                    @endif
                </div>
            </div>

            <!-- JSON completo de respuesta -->
            @if(isset($respuestaSat['datos']))
            <div style="margin-top: 20px;">
                <h4 style="color: #374151; margin-bottom: 10px;">📄 JSON Completo del SAT:</h4>
                <div class="json-viewer">
                    <pre style="background: #f0fdf4; padding: 15px; border-radius: 8px; border: 1px solid #22c55e; font-size: 12px; line-height: 1.4; overflow-x: auto; white-space: pre-wrap;">{{ json_encode($respuestaSat['datos'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) }}</pre>
                </div>
            </div>
            @endif

            <!-- Vista previa del HTML (cuando esté disponible) -->
            @if(isset($respuestaSat['html_decodificado']) && $respuestaSat['html_decodificado'])
            <div style="margin-top: 20px;">
                <h4 style="color: #374151; margin-bottom: 10px;">🖥️ Vista Previa del Documento:</h4>
                <div style="background: #fefce8; padding: 15px; border-radius: 8px; border: 1px solid #eab308;">
                    <div class="html-decoded">
                        <iframe srcdoc="{{ htmlspecialchars($respuestaSat['html_decodificado']) }}" 
                                style="width: 100%; height: 400px; border: 1px solid #d1d5db; border-radius: 4px;">
                        </iframe>
                    </div>
                    <div style="margin-top: 15px; text-align: center;">
                        <button onclick="descargarHTML()" 
                                style="background: #059669; color: white; padding: 10px 20px; border: none; border-radius: 6px; margin-right: 10px; cursor: pointer; font-weight: bold;">
                            💾 Descargar HTML
                        </button>
                        <button onclick="abrirEnNuevaVentana()" 
                                style="background: #2563eb; color: white; padding: 10px 20px; border: none; border-radius: 6px; cursor: pointer; font-weight: bold;">
                            🔗 Abrir en Nueva Ventana
                        </button>
                    </div>
                </div>
            </div>
            @endif

        @else
            <!-- Error en la comunicación con el SAT -->
             <div style="background: #fef2f2; border: 1px solid #fca5a5; border-radius: 8px; padding: 15px; color: #dc2626;">
                 <strong>❌ Error en la comunicación con el SAT</strong>
                 <p><strong>Error:</strong> {{ $respuestaSat['error'] ?? 'Error desconocido' }}</p>
                 @if(isset($respuestaSat['codigo_http']))
                     <p><strong>Código HTTP:</strong> {{ $respuestaSat['codigo_http'] }}</p>
                 @endif
                 <p><em>Nota: Esto es normal mientras no tengas configurada la URL real de la API del SAT.</em></p>
                 
                 <!-- Recordatorio de configuración -->
                 <div style="background: #fef3c7; border: 1px solid #f59e0b; border-radius: 6px; padding: 12px; margin-top: 15px; color: #92400e;">
                     <strong>📝 Recordatorio de Configuración:</strong>
                     <p style="margin: 8px 0 4px 0;">Cuando recibas las credenciales del SAT, configura:</p>
                     <ul style="margin: 5px 0; padding-left: 20px; font-size: 13px;">
                         <li><strong>Archivo:</strong> <code>.env</code> (en la raíz del proyecto)</li>
                         <li><strong>Variable:</strong> <code>SAT_API_URL=https://url-real-del-sat.gob.mx/api</code></li>
                         <li><strong>Token (si aplica):</strong> <code>SAT_API_TOKEN=tu_token_aqui</code></li>
                         <li><strong>Key (si aplica):</strong> <code>SAT_API_KEY=tu_key_aqui</code></li>
                     </ul>
                     <p style="margin: 8px 0 0 0; font-size: 12px;">
                         💡 <strong>Tip:</strong> Puedes copiar las variables desde <code>.env.example</code> y solo cambiar los valores.
                     </p>
                 </div>
             </div>

            <!-- Mostrar respuesta cruda si existe -->
            @if(isset($respuestaSat['respuesta_cruda']) && $respuestaSat['respuesta_cruda'])
            <div style="margin-top: 15px;">
                <h4 style="color: #374151; margin-bottom: 10px;">🔍 Respuesta Cruda del Servidor:</h4>
                <div class="json-viewer">
                    <pre style="background: #fef2f2; padding: 15px; border-radius: 8px; border: 1px solid #fca5a5; font-size: 12px; line-height: 1.4; overflow-x: auto; white-space: pre-wrap;">{{ $respuestaSat['respuesta_cruda'] }}</pre>
                </div>
            </div>
            @endif
        @endif
    @else
        <div style="background: #f3f4f6; border: 1px solid #d1d5db; border-radius: 8px; padding: 15px; color: #6b7280;">
            <p>⏳ No se ha enviado ninguna solicitud al SAT aún.</p>
        </div>
    @endif
</div>

<div class="caja">
    <h4>Decodificación del Archivo codigo.txt</h4>
    <p>Contenido HTML decodificado del archivo codigo.txt con soporte completo para caracteres especiales en español.</p>
    <hr>
    
    @if($resultadoDecodificacion['error'])
        <div class="alert alert-danger">
            <strong>Error:</strong> {{ $resultadoDecodificacion['mensaje'] }}
        </div>
    @else
        <div class="row">
            <div class="col-md-6">
                <h5>Datos Originales del JSON:</h5>
                <div class="json-viewer">
                    <code>{{ json_encode($resultadoDecodificacion['datos_originales'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) }}</code>
                </div>
            </div>
            <div class="col-md-6">
                <h5>Código Base64 Original:</h5>
                <div class="codigo-original">
                    {{ substr($resultadoDecodificacion['codigo_base64'], 0, 500) }}...
                    <br><small class="text-muted">(Mostrando primeros 500 caracteres)</small>
                </div>
            </div>
        </div>
        
        <div class="row" style="margin-top: 20px;">
            <div class="col-md-12">
                <h5>HTML Decodificado:</h5>
                <div class="html-decoded">
                    {!! $resultadoDecodificacion['html_decodificado'] !!}
                </div>
            </div>
        </div>
        
        <div class="row" style="margin-top: 20px;">
            <div class="col-md-12">
                <h5>Código HTML (para inspección):</h5>
                <div class="json-viewer">
                    <code>{{ htmlspecialchars($resultadoDecodificacion['html_decodificado']) }}</code>
                </div>
            </div>
        </div>
    @endif
</div>

<div class="row nav-actions" style="margin-top:20px">
    <div class="col-xs-12 text-center">
      <a href="{{ route('inicio') }}" class="btn btn-gob-outline" aria-label="Realizar otro trámite">
        Realizar otro trámite
      </a>
    </div>
</div>
<br>
@endsection

@push('scripts')
<script>
    (function () {
        // Previene que se pueda volver atrás en el historial del navegador.
        // Al intentar retroceder, simplemente se recarga la página actual.
        history.pushState(null, document.title, location.href);
        window.addEventListener('popstate', function () {
            history.pushState(null, document.title, location.href);
        });
    })();

    // ==========================================================
    //  FUNCIONES PARA DESCARGAR Y ABRIR HTML DEL SAT
    // ==========================================================
    
    /**
     * Descarga el HTML decodificado del SAT como archivo
     */
    function descargarHTML() {
        @if(isset($respuestaSat['html_decodificado']) && $respuestaSat['html_decodificado'])
        const htmlContent = {!! json_encode($respuestaSat['html_decodificado']) !!};
        
        if (htmlContent && htmlContent.trim() !== '') {
            const blob = new Blob([htmlContent], { type: 'text/html' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.style.display = 'none';
            a.href = url;
            a.download = 'documento_sat_{{ date("Y-m-d_H-i-s") }}.html';
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
            document.body.removeChild(a);
        } else {
            alert('No hay contenido HTML disponible para descargar.');
        }
        @else
        alert('No hay contenido HTML disponible para descargar.');
        @endif
    }
    
    /**
     * Abre el HTML decodificado en una nueva ventana
     */
    function abrirEnNuevaVentana() {
        @if(isset($respuestaSat['html_decodificado']) && $respuestaSat['html_decodificado'])
        const htmlContent = {!! json_encode($respuestaSat['html_decodificado']) !!};
        
        if (htmlContent && htmlContent.trim() !== '') {
            const nuevaVentana = window.open('', '_blank');
            if (nuevaVentana) {
                nuevaVentana.document.write(htmlContent);
                nuevaVentana.document.close();
            } else {
                alert('No se pudo abrir la nueva ventana. Verifica que no esté bloqueada por el navegador.');
            }
        } else {
            alert('No hay contenido HTML disponible para mostrar.');
        }
        @else
        alert('No hay contenido HTML disponible para mostrar.');
        @endif
    }
</script>
@endpush