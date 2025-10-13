@extends('layouts.base')

@section('title', 'PAGA IMT')

@section('content')
<br>
  {{-- LOGOS: centrados en XS, en extremos desde SM+ --}}
  <div class="row" style="margin-top:15px; margin-bottom:10px">
    {{-- Izquierda --}}
    <div class="col-xs-12 col-sm-6">
      {{-- Móvil (XS): centrado --}}
      <div class="visible-xs-block text-center">
        <img
          src="{{ asset('img/comunicaciones.png') }}"
          alt="Secretaría de Infraestructura, Comunicaciones y Transportes"
          class="img-responsive"
          style="display:inline-block; max-height:56px">
      </div>
      {{-- Tablet/Escritorio (SM+): alineado a la izquierda --}}
      <div class="hidden-xs">
        <img
          src="{{ asset('img/comunicaciones.png') }}"
          alt="Secretaría de Infraestructura, Comunicaciones y Transportes"
          class="img-responsive"
          style="max-height:80px">
      </div>
    </div>

    {{-- Derecha --}}
    <div class="col-xs-12 col-sm-6">
      {{-- Móvil (XS): centrado --}}
      <div class="visible-xs-block text-center">
        <img
          src="{{ asset('img/imt.png') }}"
          alt="Instituto Mexicano del Transporte"
          class="img-responsive"
          style="display:inline-block; max-height:48px">
      </div>
      {{-- Tablet/Escritorio (SM+): alineado a la derecha --}}
      <div class="hidden-xs text-right">
        <img
          src="{{ asset('img/imt.png') }}"
          alt="Instituto Mexicano del Transporte"
          class="img-responsive"
          style="display:inline-block; max-height:80px">
      </div>
    </div>
  </div>
<br>

  {{-- TÍTULO: centrado en XS, alineado normal en SM+ --}}
  <div class="hidden-xs">
    <h1 style="margin:10px 0 6px;">Bienvenido a PAGA IMT</h1>
    <div style="height:4px; width:48px; background:#a57f2c; margin:6px 0 18px;"></div>
  </div>

  <div class="visible-xs-block text-center">
    <h1 style="margin:10px 0 6px;">Bienvenido a PAGA IMT</h1>
    <div style="height:4px; width:48px; background:#a57f2c; margin:6px auto 18px;"></div>
  </div>

  {{-- PANEL ÚNICO --}}
  <div class="row" style="margin-top:10px">
    <div class="col-xs-12">
      <div class="panel panel-default">
        <div class="panel-heading">
          Secretaría de Infraestructura, Comunicaciones y Transportes
        </div>
        <div class="panel-body">
          {{-- Iteramos por las dependencias --}}
          @foreach ($dependencias as $dependencia)
            <div>
              {{-- Al hacer clic, guardamos el ID de la dependencia en la sesión y redirigimos --}}
              <a href="{{ route('tramite.store') }}" style="text-decoration:underline;" 
                 onclick="event.preventDefault(); document.getElementById('dependencia-form-{{ $dependencia->id }}').submit();">
                {{ $dependencia->nombre }}
              </a>
              {{-- Formulario oculto para guardar el ID de la dependencia --}}
              <form id="dependencia-form-{{ $dependencia->id }}" action="{{ route('tramite.store') }}" method="POST" style="display: none;">
                  @csrf
                  <input type="hidden" name="dependenciaId" value="{{ $dependencia->id }}">
              </form>
            </div>
          @endforeach
        </div>
      </div>
      <p style="margin-top:20px; color:#666; font-size:13px;">
        Selecciona la dependencia para iniciar el trámite de pago y generación de línea de captura.
      </p>
      <br>
    </div>
  </div>
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