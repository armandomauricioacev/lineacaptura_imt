@extends('layouts.base')

@section('title', 'Selección de trámite')

@section('content')
  <style>
    /* ... (Tu CSS se mantiene igual) ... */
    :root{ --gob-rojo:#611232; }
    #pasos .nav-pills{ display:flex; justify-content:center; align-items:stretch; gap:12px; padding-left:0; flex-wrap:nowrap; }
    #pasos .nav-pills>li{ float:none; display:block; }
    #pasos .nav-pills>li>a{ width:220px; min-height:64px; display:flex; flex-direction:column; align-items:center; justify-content:center; text-align:center; line-height:1.2; text-decoration:none; color:#111; background:#fff; border:1px solid #ddd; border-radius:4px; padding:8px 14px; cursor:default; pointer-events:none; }
    #pasos .nav-pills>li.active>a{ background:var(--gob-rojo); color:#fff !important; border-color:var(--gob-rojo); }
    #pasos .nav-pills>li.active>a small{ color:#fff !important; }
    .btn-gob-outline{ background:#fff !important; color:var(--gob-rojo) !important; border:2px solid var(--gob-rojo) !important; box-shadow:none !important; text-decoration:none !important; }
    .btn-gob-outline:hover, .btn-gob-outline:focus{ background:var(--gob-rojo) !important; color:#fff !important; border-color:var(--gob-rojo) !important; box-shadow:none !important; text-decoration:none !important; outline: none !important; }
    .btn-quitar { color: #a94442; background: transparent; border: none; font-size: 1.5em; line-height: 1; padding: 0 5px; cursor: pointer; }
    .btn-quitar:hover { color: #7a2b29; }
    
    /* ========================================================== */
    /* INICIO DE LAS CORRECCIONES DE RESPONSIVIDAD                */
    /* ========================================================== */
    @media (max-width: 767px) {
        /* Ocultamos los encabezados originales de la tabla */
        .tabla-tramites thead {
            display: none;
        }
        /* Ocultamos el pie de tabla original */
        .tabla-tramites tfoot {
            display: none;
        }
        /* Convertimos cada fila en un bloque separado (como una tarjeta) */
        .tabla-tramites tr {
            display: block;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin-bottom: 20px;
            padding: 15px;
        }
        /* Convertimos cada celda en un bloque y alineamos el contenido */
        .tabla-tramites td {
            display: block;
            text-align: right;
            position: relative;
            padding-left: 50%;
            border-bottom: 1px solid #eee;
            padding-top: 10px;
            padding-bottom: 10px;
            word-wrap: break-word;
        }
        /* La primera celda (Descripción) se trata como un título */
        .tabla-tramites td:first-child {
            padding-left: 0;
            text-align: left;
            font-weight: bold;
            font-size: 1.1em;
            border-bottom: 1px solid #ccc;
            margin-bottom: 10px;
        }
        /* Se añade la etiqueta de texto antes del contenido para las demás celdas */
        .tabla-tramites td:not(:first-child):not(:last-child)::before {
            content: attr(data-label);
            position: absolute;
            left: 0;
            font-weight: 600;
            color: #555;
            text-align: left;
        }
        /* Ocultamos la etiqueta para la descripción */
        .tabla-tramites td:first-child::before {
            display: none;
        }
        /* Estilo especial para la celda del botón de eliminar */
        .tabla-tramites td:last-child {
            border-bottom: none;
            padding: 10px 0 0;
            text-align: right;
        }
        .tabla-tramites td:last-child::before {
            display: none;
        }

    }

    @media (max-width:575px){
      #pasos .nav-pills{ flex-direction:column; align-items:center; flex-wrap:nowrap; }
      #pasos .nav-pills>li>a{ width:100%; max-width:280px; min-width:240px; }
      .nav-actions .btn{ width:100%; display:inline-block; }
      .nav-actions .col-xs-6{ width:100%; float:none; }
      .nav-actions .col-xs-6 + .col-xs-6{ margin-top:10px; text-align:center; }
    }
    /* ========================================================== */
    /* FIN DE LAS CORRECCIONES                                    */
    /* ========================================================== */
  </style>

  {{-- Breadcrumb --}}
  <ol class="breadcrumb" style="margin-top:10px">
    <li><a href="{{ url('/') }}">Inicio</a></li>
    <li>{{ $dependencia->nombre }}</li>
  </ol>

  <div id="alert-placeholder" style="margin-top: 15px;"></div>

  {{-- Título y Pasos --}}
  <center><h1 style="margin:10px 0 6px;">{{ $dependencia->nombre }}</h1></center>
  <br>
  <div id="pasos" class="text-center" style="margin-bottom:20px">
    <ul class="nav nav-pills">
      <li class="active"><a href="#" aria-current="step"><strong>Paso 1</strong><small>Selección del trámite</small></a></li>
      <li><a href="#"><strong>Paso 2</strong><small>Información de la persona</small></a></li>
      <li><a href="#"><strong>Paso 3</strong><small>Formato de pago</small></a></li>
    </ul>
  </div>

  {{-- Sección de selección --}}
  <h3 style="margin-top:0">Selección de trámites:</h3>
  <div style="height:4px; width:48px; background:#a57f2c; margin:6px 0 18px;"></div>
  <p>Identifique y seleccione sus trámites y/o servicios.</p>

  <form id="tramiteForm" action="{{ route('persona.store') }}" method="POST">
    @csrf
    
    <div class="form-group" style="margin-bottom: 20px;">
        <label for="tramiteSelect" style="font-weight: bold; font-size: 1.2em;">Lista de trámites y/o servicios</label>
        <select class="form-control" id="tramiteSelect">
            <option value="" selected disabled>Selecciona un trámite para añadirlo a tu lista</option>
            @foreach ($tramites as $tramite)
                <option value="{{ $tramite->id }}" data-descripcion="{{ $tramite->descripcion }}" data-cuota="{{ $tramite->cuota }}" data-iva="{{ $tramite->iva }}">
                    {{ $tramite->descripcion }} - ${{ number_format($tramite->cuota, 2) }} MXN
                </option>
            @endforeach
        </select>
    </div>
    
    <div id="tramites-hidden-container"></div>

    <h4>Trámites seleccionados:</h4>
    {{-- SE ELIMINÓ EL DIV "table-responsive" Y SE AÑADIÓ LA CLASE "tabla-tramites" A LA TABLA --}}
    <table class="table table-striped tabla-tramites">
      <thead>
        <tr>
          <th>Descripción del concepto</th>
          <th class="text-right">Importe</th>
          <th class="text-right">IVA</th>
          <th class="text-right">Total</th>
          <th class="text-center">Eliminar</th>
        </tr>
      </thead>
      <tbody id="tramitesSeleccionadosBody">
          <tr id="empty-row">
              <td colspan="5" class="text-center" style="padding: 20px; color: #777;">Aún no has agregado trámites.</td>
          </tr>
      </tbody>
      <tfoot>
          <tr style="font-weight: bold; font-size: 1.2em;">
              <td colspan="3" class="text-right">Total a pagar:</td>
              <td id="total-general" class="text-right">$0.00 MXN</td>
              <td></td>
          </tr>
      </tfoot>
    </table>

    <div class="row nav-actions" style="margin-top:10px">
      <div class="col-xs-6">
        <a href="{{ url('/') }}" class="btn btn-gob-outline">Regresar</a>
      </div>
      <div class="col-xs-6 text-right">
        <button type="submit" class="btn btn-gob-outline" id="btnSiguiente">Siguiente</button>
      </div>
    </div>
  </form>

  <p style="margin-top:15px; color:#777; font-size:12px">* Campos obligatorios</p>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const tramiteForm = document.getElementById('tramiteForm');
    const tramiteSelect = document.getElementById('tramiteSelect');
    const tramitesSeleccionadosBody = document.getElementById('tramitesSeleccionadosBody');
    const hiddenContainer = document.getElementById('tramites-hidden-container');
    const totalGeneralCell = document.getElementById('total-general');
    const alertPlaceholder = document.getElementById('alert-placeholder');
    const emptyRow = document.getElementById('empty-row');

    let tramitesSeleccionados = [];
    const MAX_TRAMITES = 10;
    
    function showAlert(message, type = 'warning') {
        const alertHTML = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
              <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              <strong>¡Atención!</strong> ${message}
            </div>`;
        alertPlaceholder.innerHTML = alertHTML;

        const closeButton = alertPlaceholder.querySelector('.close');
        if (closeButton) {
            closeButton.addEventListener('click', function() {
                this.closest('.alert').remove();
            });
        }
    }
    
    function actualizarVista() {
        tramitesSeleccionadosBody.innerHTML = '';
        hiddenContainer.innerHTML = '';
        let totalGeneral = 0;

        if (tramitesSeleccionados.length === 0) {
            tramitesSeleccionadosBody.appendChild(emptyRow);
        } else {
            tramitesSeleccionados.forEach((tramite, index) => {
                const ivaMonto = tramite.iva == '1' ? parseFloat(tramite.cuota) * 0.16 : 0;
                const totalTramite = parseFloat(tramite.cuota) + ivaMonto;
                totalGeneral += totalTramite;
                
                // === SE AÑADIERON LOS ATRIBUTOS data-label A CADA CELDA ===
                const newRow = `
                    <tr data-id="${tramite.id}">
                        <td data-label="Descripción del concepto">${tramite.descripcion}</td>
                        <td data-label="Importe" class="text-right">${formatCurrency(tramite.cuota)}</td>
                        <td data-label="IVA" class="text-right">${formatCurrency(ivaMonto)}</td>
                        <td data-label="Total" class="text-right">${formatCurrency(totalTramite)}</td>
                        <td data-label="Eliminar" class="text-center">
                            <button type="button" class="btn-quitar" data-index="${index}" title="Quitar trámite">&times;</button>
                        </td>
                    </tr>`;
                tramitesSeleccionadosBody.innerHTML += newRow;
                
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'tramite_ids[]';
                hiddenInput.value = tramite.id;
                hiddenContainer.appendChild(hiddenInput);
            });
        }
        
        totalGeneralCell.textContent = formatCurrency(totalGeneral) + ' MXN';
    }

    function formatCurrency(value) {
        return new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(value);
    }

    tramiteSelect.addEventListener('change', function() {
        alertPlaceholder.innerHTML = '';
        const selectedOption = this.options[this.selectedIndex];
        if (!selectedOption.value) return;

        if (tramitesSeleccionados.length >= MAX_TRAMITES) {
            showAlert(`No puedes agregar más de ${MAX_TRAMITES} trámites.`);
            this.selectedIndex = 0;
            return;
        }

        const tramiteId = selectedOption.value;
        if (tramitesSeleccionados.some(t => t.id === tramiteId)) {
            showAlert('Este trámite ya ha sido agregado a la lista.');
            this.selectedIndex = 0;
            return;
        }

        const tramiteData = {
            id: tramiteId,
            descripcion: selectedOption.getAttribute('data-descripcion'),
            cuota: selectedOption.getAttribute('data-cuota'),
            iva: selectedOption.getAttribute('data-iva'),
        };
        
        tramitesSeleccionados.push(tramiteData);
        actualizarVista();
        this.selectedIndex = 0;
    });

    tramitesSeleccionadosBody.addEventListener('click', function(event) {
        if (event.target.classList.contains('btn-quitar')) {
            const indexToRemove = parseInt(event.target.getAttribute('data-index'), 10);
            tramitesSeleccionados.splice(indexToRemove, 1);
            actualizarVista();
        }
    });

    tramiteForm.addEventListener('submit', function (event) {
        if (tramitesSeleccionados.length === 0) {
            event.preventDefault();
            showAlert('Debes agregar al menos un trámite para poder continuar.');
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    });
});
</script>
@endpush