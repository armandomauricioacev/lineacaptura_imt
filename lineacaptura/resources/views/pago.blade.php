@extends('layouts.base')

@section('title', 'Resumen de pago')

@section('content')
  <style>
    /* ... (Tu CSS base se mantiene igual) ... */
    :root{ --gob-rojo:#611232; }
    #pasos .nav-pills{ display:flex; justify-content:center; align-items:stretch; gap:12px; padding-left:0; flex-wrap:nowrap; }
    #pasos .nav-pills>li>a{ width:220px; min-height:64px; display:flex; flex-direction:column; align-items:center; justify-content:center; text-align:center; line-height:1.2; text-decoration:none; color:#111; background:#fff; border:1px solid #ddd; border-radius:4px; padding:8px 14px; cursor:default; pointer-events:none; }
    #pasos .nav-pills>li.active>a{ background:var(--gob-rojo); color:#fff; border-color:var(--gob-rojo); }
    #pasos .nav-pills>li.active>a small{ color:#fff; }
    .btn-gob-outline{ background:#fff !important; color:var(--gob-rojo) !important; border:2px solid var(--gob-rojo) !important; box-shadow:none !important; text-decoration:none !important; }
    .btn-gob-outline:hover, .btn-gob-outline:focus{ background:var(--gob-rojo) !important; color:#fff !important; border-color:var(--gob-rojo) !important; box-shadow:none !important; text-decoration:none !important; outline: none !important; }
    
    /* ========================================================== */
    /* INICIO DE MODIFICACIONES CSS                               */
    /* ========================================================== */

    /* 1. Contenedor para alinear cajas horizontalmente */
    .equal-panels {
        display: flex;
        flex-wrap: wrap;
        margin-bottom: 20px;
    }
    .equal-panels > [class*="col-"] {
        display: flex;
        padding-left: 10px;
        padding-right: 10px;
    }
    .caja {
        border:1px solid #e5e5e5;
        border-radius:6px;
        padding:16px;
        background:#fff;
        width: 100%;
    }

    /* 2. Ajustes para la lista de descripción (datos de persona) */
    .resumen.dl-horizontal dt {
        color: #555;
        font-weight: 600; /* Semi-negrita */
        width: 160px;
        text-align: left;
        white-space: normal;
    }
    .resumen.dl-horizontal dd {
        margin-left: 180px;
        margin-bottom: 10px;
        text-align: left;
    }

    /* 3. Estilo para el total general fuera de los recuadros */
    .total-container {
        text-align: center;
        margin-top: 20px;
        padding: 20px;
        border-top: 1px solid #eee;
    }
    .total-container .total-label {
        font-size: 1.5em;
        color: #333;
        font-weight: normal; /* Se quita la negrita */
    }
    .total-container .total-amount {
        font-size: 2em;
        color: #000;
        font-weight: normal; /* Se quita la negrita */
        display: block;
        margin-top: 5px;
    }

    /* 4. Estilo de los encabezados de la tabla para que coincidan con los de 'Datos de la persona' */
    .table > thead > tr > th {
        font-weight: 600; /* Semi-negrita, igual que dt */
        color: #555;     /* Mismo color que dt */
    }
    
    /* Ajustes para móviles */
    @media (max-width: 991px) {
        .equal-panels > [class*="col-"] {
            margin-bottom: 20px;
        }
    }
    @media (max-width: 767px) {
        .resumen.dl-horizontal dt { float: none; width: auto; }
        .resumen.dl-horizontal dd { margin-left: 0; }
    }
    /* ========================================================== */
    /* FIN DE MODIFICACIONES CSS                                  */
    /* ========================================================== */
  </style>
  
  {{-- Breadcrumb, Título y Pasos (sin cambios) --}}
  <ol class="breadcrumb" style="margin-top:10px">
    <li><a href="{{ url('/') }}">Inicio</a></li>
    <li><a href="{{ url('/persona') }}">Instituto Mexicano del Transporte</a></li>
  </ol>
  <center><h1 style="margin:10px 0 6px;">Instituto Mexicano del Transporte</h1></center>
  <br>
  <div id="pasos" class="text-center" style="margin-bottom:20px">
    <ul class="nav nav-pills">
        <li><a href="#"><strong>Paso 1</strong><small>Selección del trámite</small></a></li>
        <li><a href="#"><strong>Paso 2</strong><small>Información de la persona</small></a></li>
        <li class="active"><a href="#" aria-current="step"><strong>Paso 3</strong><small>Formato de pago</small></a></li>
    </ul>
  </div>

  <h3 style="margin-top:0">Resumen del trámite y datos de la persona:</h3>
  <div style="height:4px; width:48px; background:#a57f2c; margin:6px 0 18px;"></div>

  <div class="row equal-panels">
    {{-- Resumen de la persona --}}
    <div class="col-md-6">
      <div class="caja">
        <center><h4>Datos de la persona</h4></center>
        <hr>
        <dl class="dl-horizontal resumen">
          <dt>Tipo</dt><dd>{{ $personaData['tipo_persona'] === 'fisica' ? 'Persona Física' : 'Persona Moral' }}</dd>
          @if ($personaData['tipo_persona'] === 'fisica')
            <dt>CURP</dt><dd>{{ $personaData['curp'] }}</dd>
            <dt>RFC</dt><dd>{{ $personaData['rfc'] }}</dd>
            <dt>Nombre completo</dt><dd>{{ $personaData['nombres'] }} {{ $personaData['apellido_paterno'] }} {{ $personaData['apellido_materno'] }}</dd>
          @else
            <dt>RFC</dt><dd>{{ $personaData['rfc'] }}</dd>
            <dt>Razón social</dt><dd>{{ $personaData['razon_social'] }}</dd>
          @endif
        </dl>
      </div>
    </div>

    {{-- Resumen de trámites seleccionados --}}
    <div class="col-md-6">
        <div class="caja">
            <center><h4>Trámites seleccionados</h4></center>
            <hr>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Descripción</th>
                            <th class="text-right">Cuota</th>
                            <th class="text-right">IVA</th>
                            <th class="text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $totalGeneral = 0; @endphp
                        @foreach($tramites as $tramite)
                            @php
                                $montoIva = $tramite->iva ? round($tramite->cuota * 0.16, 2) : 0;
                                $totalTramite = $tramite->cuota + $montoIva;
                                $totalGeneral += $totalTramite;
                            @endphp
                            <tr>
                                <td>{{ $tramite->descripcion }}</td>
                                <td class="text-right">${{ number_format($tramite->cuota, 2) }}</td>
                                <td class="text-right">${{ number_format($montoIva, 2) }}</td>
                                <td class="text-right">${{ number_format($totalTramite, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
  </div>

  {{-- Nuevo contenedor para el total general --}}
  <div class="total-container">
    <span class="total-label">Importe total a pagar:</span>
    <span class="total-amount">${{ number_format($totalGeneral, 2) }} MXN</span>
  </div>

  {{-- Acciones (sin cambios) --}}
  <div class="row nav-actions" style="margin-top:16px">
    <div class="col-xs-6">
      <a href="{{ url('/persona') }}" class="btn btn-gob-outline" aria-label="Regresar al paso anterior">Regresar</a>
    </div>
    <div class="col-xs-6 text-right">
      <form action="{{ route('linea.generar') }}" method="POST" style="display: inline;">
        @csrf
        <button type="submit" class="btn btn-gob-outline" aria-label="Generar línea de captura">Generar línea de captura</button>
      </form>
    </div>
  </div>
  <br>
@endsection