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
</style>

<div class="alert alert-success">
    <strong>¡Éxito!</strong> La información ha sido guardada en la base de datos con el ID: {{ $lineaCapturada->id }}
</div>

<div class="caja">
    <h4>JSON Estático (simulación de envío al SAT)</h4>
    <p>Esta es la estructura del JSON que se enviaría al Web Service del SAT basado en la información proporcionada.</p>
    <hr>
    <div class="json-viewer"><code>{{ $jsonParaSat }}</code></div>
</div>

<div class="row nav-actions" style="margin-top:20px">
    <div class="col-xs-12 text-center">
      <a href="{{ url('/') }}" class="btn btn-gob-outline" aria-label="Realizar otro trámite">
        Realizar otro trámite
      </a>
    </div>
</div>
<br>
@endsection

{{-- ========================================================== --}}
{{-- INICIO DE LA CORRECCIÓN                                    --}}
{{-- ========================================================== --}}
@push('scripts')
<script>
    (function () {
        // 1. Añade una entrada "falsa" al historial del navegador.
        // Esto crea una "trampa". Cuando el usuario presione "atrás",
        // volverá a esta misma página, pero activará el evento 'popstate'.
        history.pushState(null, document.title, location.href);

        // 2. Escucha el evento 'popstate', que se dispara con el botón de retroceso.
        window.addEventListener('popstate', function (event) {
            // 3. En lugar de permitir que el navegador regrese, forzamos una
            // redirección a la página de inicio.
            // Usamos 'replace' para que el usuario no pueda volver a esta página con "adelante".
            window.location.replace("{{ url('/inicio') }}");
        });
    })();
</script>
@endpush
{{-- ========================================================== --}}
{{-- FIN DE LA CORRECCIÓN                                       --}}
{{-- ========================================================== --}}