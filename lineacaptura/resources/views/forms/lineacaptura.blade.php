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
</script>
@endpush