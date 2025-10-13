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
    /* INICIO DE LA MODIFICACIÓN DE ESTILOS                       */
    /* Se añaden los estilos del contenedor del total como en pago.blade.php */
    /* ========================================================== */
    .total-container { text-align: center; margin-top: 20px; padding: 20px; border-top: 1px solid #eee; }
    .total-container .total-label { font-size: 1.5em; color: #333; font-weight: normal; }
    .total-container .total-amount { font-size: 2em; color: #000; font-weight: normal; display: block; margin-top: 5px; }
    
    @media (max-width: 767px) {
        .tabla-tramites thead { display: none; }
        .tabla-tramites tr {
            display: block; border: 1px solid #ddd; border-radius: 4px;
            margin-bottom: 20px; padding: 15px;
        }
        .tabla-tramites td {
            display: block; text-align: right; position: relative; padding-left: 50%;
            border-bottom: 1px solid #eee; padding-top: 10px; padding-bottom: 10px; word-wrap: break-word;
        }
        .tabla-tramites td:last-child { border-bottom: none; }
        .tabla-tramites td:first-child {
            padding-left: 0; text-align: left; font-weight: bold; font-size: 1.1em;
            border-bottom: 1px solid #ccc; margin-bottom: 10px;
        }
        .tabla-tramites td:not(:first-child)::before {
            content: attr(data-label); position: absolute; left: 0;
            font-weight: 600; color: #555; text-align: left;
        }
        .tabla-tramites td:first-child::before { display: none; }
    }

    @media (max-width:575px){
      #pasos .nav-pills{ flex-direction:column; align-items:center; flex-wrap:nowrap; }
      #pasos .nav-pills>li>a{ width:100%; max-width:280px; min-width:240px; }
      .nav-actions .btn{ width:100%; display:inline-block; }
      .nav-actions .col-xs-6{ width:100%; float:none; }
      .nav-actions .col-xs-6 + .col-xs-6{ margin-top:10px; text-align:center; }
    }
  </style>

  {{-- Breadcrumb --}}
  <ol class="breadcrumb" style="margin-top:10px">
    <li><a href="{{ url('/') }}">Inicio</a></li>
    <li>Instituto Mexicano del Transporte</li>
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
    <table class="table tabla-tramites">
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
      {{-- Se elimina el tfoot de aquí --}}
    </table>
  </form>

  {{-- ========================================================== --}}
  {{-- INICIO DE LA MODIFICACIÓN                                  --}}
  {{-- Se añade el nuevo contenedor para el total.                --}}
  {{-- ========================================================== --}}
  <div class="total-container">
    <span class="total-label">Importe total a pagar:</span>
    <span class="total-amount" id="total-general">$0.00 MXN</span>
  </div>

  {{-- La navegación ahora está fuera del formulario principal --}}
  <div class="row nav-actions" style="margin-top:10px">
    <div class="col-xs-6">
      <form action="{{ route('regresar') }}" method="POST" style="display: inline;">
          @csrf
          <input type="hidden" name="paso_actual" value="tramite">
          <button type="submit" class="btn btn-gob-outline" aria-label="Regresar al paso anterior">Regresar</button>
      </form>
    </div>
    <div class="col-xs-6 text-right">
      <button type="submit" class="btn btn-gob-outline" id="btnSiguiente" form="tramiteForm">Siguiente</button>
    </div>
  </div>

  <p style="margin-top:15px; color:#777; font-size:12px">* Campos obligatorios</p>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const tramiteForm = document.getElementById('tramiteForm');
    const tramiteSelect = document.getElementById('tramiteSelect');
    const tramitesSeleccionadosBody = document.getElementById('tramitesSeleccionadosBody');
    const hiddenContainer = document.getElementById('tramites-hidden-container');
    const totalGeneralSpan = document.getElementById('total-general'); // <--- CAMBIO AQUÍ
    const alertPlaceholder = document.getElementById('alert-placeholder');
    const emptyRow = document.getElementById('empty-row');
    const btnSiguiente = document.getElementById('btnSiguiente');

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
        
        // --- CAMBIO AQUÍ: Se actualiza el nuevo span ---
        totalGeneralSpan.textContent = formatCurrency(totalGeneral) + ' MXN';
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

    // --- CAMBIO AQUÍ: El listener ahora apunta al botón 'Siguiente' ---
    btnSiguiente.addEventListener('click', function (event) {
        if (tramitesSeleccionados.length === 0) {
            event.preventDefault();
            showAlert('Debes agregar al menos un trámite para poder continuar.');
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    });

    // Script para bloquear las flechas del navegador
    history.pushState(null, document.title, location.href);
    window.addEventListener('popstate', function () {
        history.pushState(null, document.title, location.href);
    });
});
</script>
@endpush