@extends('layouts.base')

@section('title', 'Formato de pago · IMT')

@section('content')
  {{-- Tus estilos CSS se quedan exactamente igual --}}
  <style>
    :root{ --gob-rojo:#611232; }
    #pasos .nav-pills{ display:flex; justify-content:center; align-items:stretch; gap:12px; padding-left:0; flex-wrap:nowrap; }
    #pasos .nav-pills>li{ float:none; display:block; }
    #pasos .nav-pills>li>a{ width:220px; min-height:64px; display:flex; flex-direction:column; align-items:center; justify-content:center; text-align:center; line-height:1.2; text-decoration:none; color:#111; background:#fff; border:1px solid #ddd; border-radius:4px; padding:8px 14px; cursor:default; pointer-events:none; }
    #pasos .nav-pills>li>a small{ display:block; margin-top:2px; color:#666; }
    #pasos .nav-pills>li.active>a,
    #pasos .nav-pills>li.active>a:focus,
    #pasos .nav-pills>li.active>a:hover{ background:var(--gob-rojo); color:#fff; border-color:var(--gob-rojo); }
    #pasos .nav-pills>li.active>a small{ color:#fff; }
    .btn-gob-outline{ background:#fff !important; color:var(--gob-rojo) !important; border:2px solid var(--gob-rojo) !important; box-shadow:none !important; text-decoration:none !important; }
    .btn-gob-outline:hover,
    .btn-gob-outline:focus{ background:var(--gob-rojo) !important; color:#fff !important; border-color:var(--gob-rojo) !important; box-shadow:none !important; text-decoration:none !important; }
    .equal-panels{ display:flex; flex-wrap:wrap; }
    .equal-panels>[class*="col-"]{ display:flex; }
    .equal-panels .caja{ display:flex; flex-direction:column; width:100%; }
    .resumen dt{ color:#555; font-weight:600; }
    .resumen dd{ margin-bottom:10px; }
    .caja{ border:1px solid #e5e5e5; border-radius:6px; padding:16px; background:#fff; }
    .caja h4{ margin-top:0; }
    .importe{ font-size:22px; font-weight:700; }
    .resumen.dl-horizontal dt{ width: 220px; text-align: left; white-space: normal; overflow: visible; text-overflow: clip; }
    .resumen.dl-horizontal dd{ margin-left: 240px; text-align: left; white-space: normal; }
    @media (max-width:575px){
      #pasos .nav-pills{ flex-direction:column; align-items:center; flex-wrap:nowrap; }
      #pasos .nav-pills>li>a{ width:100%; max-width:280px; min-width:240px; }
      .nav-actions .btn{ width:100%; display:inline-block; }
      .nav-actions .col-xs-6{ width:100%; float:none; }
      .nav-actions .col-xs-6 + .col-xs-6{ margin-top:10px; text-align:center; }
      .equal-panels .caja{ margin-bottom:10px; }
      .resumen.dl-horizontal dt{ float:none; width:auto; }
      .resumen.dl-horizontal dd{ margin-left:0; }
    }
  </style>
  
  {{-- Breadcrumb --}}
  <ol class="breadcrumb" style="margin-top:10px">
    <li><a href="{{ url('/') }}">Inicio</a></li>
    <li><a href="{{ url('/persona') }}">Instituto Mexicano del Transporte</a></li>
  </ol>

  {{-- Título --}}
  <center><h1 style="margin:10px 0 6px;">Instituto Mexicano del Transporte</h1></center>
  <br>

  {{-- Pasos (Paso 3 activo) --}}
  <div id="pasos" class="text-center" style="margin-bottom:20px">
    <ul class="nav nav-pills">
        <li><a href="#"><strong>Paso 1</strong><small>Selección del trámite</small></a></li>
        <li><a href="#"><strong>Paso 2</strong><small>Información de la persona</small></a></li>
        <li class="active"><a href="#" aria-current="step"><strong>Paso 3</strong><small>Formato de pago</small></a></li>
    </ul>
  </div>

  {{-- Encabezado --}}
  <h3 style="margin-top:0">Resumen del trámite y la persona:</h3>
  <div style="height:4px; width:48px; background:#a57f2c; margin:6px 0 18px;"></div>

  <div class="row equal-panels">
    {{-- Resumen del trámite --}}
    <div class="col-md-6">
      <div class="caja">
        <h4>Trámite seleccionado</h4>
        <dl class="dl-horizontal resumen">
          <dt>Dependencia</dt><dd>{{ $dependencia->nombre }}</dd>
          <dt>Clave de dependencia</dt><dd>{{ $dependencia->clave_dependencia }}</dd>
          <dt>Unidad administrativa</dt><dd>{{ $dependencia->unidad_administrativa }}</dd>
          <dt>Descripción</dt><dd>{{ $tramite->descripcion }}</dd>
          <dt>Cuota</dt><dd class="importe">${{ number_format($tramite->cuota, 2) }} MXN</dd>
          <dt>IVA</dt><dd class="importe">${{ number_format($tramite->cuota * $tramite->porcentaje_iva, 2) }} MXN</dd>
          <dt>Importe Total</dt><dd class="importe">${{ number_format($tramite->total_tramite, 2) }} MXN</dd>
        </dl>
      </div>
    </div>

    {{-- Resumen de la persona --}}
    <div class="col-md-6">
      <div class="caja">
        <h4>Datos de la persona</h4>
        <dl class="dl-horizontal resumen">
          <dt>Tipo</dt><dd>{{ $personaData['tipo_persona'] === 'fisica' ? 'Persona Física' : 'Persona Moral' }}</dd>

          @if ($personaData['tipo_persona'] === 'fisica')
            {{-- CAMPOS PARA PERSONA FÍSICA --}}
            <dt>CURP</dt><dd>{{ $personaData['curp'] }}</dd>
            <dt>RFC</dt><dd>{{ $personaData['rfc'] }}</dd>
            <dt>Nombre Completo</dt><dd>{{ $personaData['nombres'] }} {{ $personaData['apellido_paterno'] }} {{ $personaData['apellido_materno'] }}</dd>
          @else
            {{-- CAMPOS PARA PERSONA MORAL --}}
            <dt>RFC</dt><dd>{{ $personaData['rfc'] }}</dd>
            <dt>Razón social</dt><dd>{{ $personaData['razon_social'] }}</dd>
          @endif
        </dl>
      </div>
    </div>
  </div>

  {{-- Acciones --}}
  <div class="row nav-actions" style="margin-top:16px">
    <div class="col-xs-6">
      <a href="{{ url('/persona') }}" class="btn btn-gob-outline" aria-label="Regresar al paso anterior">
        Regresar
      </a>
    </div>
    <div class="col-xs-6 text-right">
      {{-- Este botón ahora debería enviar a un controlador para guardar y generar la línea --}}
      <a href="#" class="btn btn-gob-outline" aria-label="Generar línea de captura">
        Generar línea de captura
      </a>
    </div>
  </div>
  <br>
@endsection