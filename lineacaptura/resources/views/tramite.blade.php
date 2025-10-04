@extends('layouts.base')

@section('title', 'Selección de trámite · ' . $dependencia->nombre)

@section('content')
  <style>
    /* ===== Ajustes header/footer del framework GOB.MX (igual que tenías) ===== */
    header .navbar-toggle .icon-bar,
    .gm-navbar .navbar-toggle .icon-bar,
    .navbar-header .navbar-toggle .icon-bar { background-color:#fff !important; border-color:#fff !important; }
    .gm-navbar .navbar-toggle, .navbar-header .navbar-toggle, header .navbar-toggle {
      border:2px solid rgba(255,255,255,0.5) !important; background-color:transparent !important;
    }
    .gm-navbar .navbar-toggle:hover, .gm-navbar .navbar-toggle:focus,
    header .navbar-toggle:hover, header .navbar-toggle:focus {
      background-color:rgba(255,255,255,0.1) !important; border-color:#fff !important;
    }
    .gm-navbar .navbar-header, header .navbar-header { display:flex !important; align-items:center !important; min-height:60px !important; }
    .gm-navbar .navbar-toggle, header .navbar-toggle { margin-top:0 !important; margin-bottom:0 !important; align-self:center !important; }
    .gm-navbar .navbar-brand, header .navbar-brand { align-self:center !important; }
    header svg, .gm-navbar svg, .navbar-toggle svg { fill:#fff !important; color:#fff !important; }

    footer, .gm-footer, .page-footer { color:#fff !important; }
    footer a, .gm-footer a, .page-footer a { color:#fff !important; }
    footer .dropdown-menu, .gm-footer .dropdown-menu, .page-footer .dropdown-menu { background-color:#611232 !important; }
    footer .dropdown-menu a, .gm-footer .dropdown-menu a, .page-footer .dropdown-menu a { color:#fff !important; }
    footer .dropdown-menu li a:link, footer .dropdown-menu li a:visited,
    .gm-footer .dropdown-menu li a:link, .gm-footer .dropdown-menu li a:visited { color:#fff !important; }

    @media (max-width:768px){
      .gm-navbar .navbar-toggle .icon-bar{ background-color:#fff !important; box-shadow:none !important; }
      .gm-footer, .gm-footer a, .gm-footer .dropdown-menu a{ color:#fff !important; }
    }
    :root{ --gob-rojo:#611232; }

    /* ====== PASOS: desktop una fila centrada (sin wrap) ====== */
    #pasos .nav-pills{
      display:flex;
      justify-content:center;
      align-items:stretch;
      gap:12px;
      padding-left:0;
      flex-wrap:nowrap;       /* clave: no envolver en desktop */
    }
    #pasos .nav-pills>li{ float:none; display:block; }
    #pasos .nav-pills>li>a{
      width:220px; min-height:64px;
      display:flex; flex-direction:column; align-items:center; justify-content:center;
      text-align:center; line-height:1.2;
      text-decoration:none; color:#111; background:#fff;
      border:1px solid #ddd; border-radius:4px; padding:8px 14px;
      cursor:default; pointer-events:none;
    }
    #pasos .nav-pills>li>a small{ display:block; margin-top:2px; color:#666; }
    #pasos .nav-pills>li.active>a,
    #pasos .nav-pills>li.active>a:focus,
    #pasos .nav-pills>li.active>a:hover{
      background:var(--gob-rojo); color:#fff !important; border-color:var(--gob-rojo);
    }
    #pasos .nav-pills>li.active>a small{ color:#fff !important; }

    /* ====== SOLO en celular (≤575px): apilar ====== */
    @media (max-width:575px){
      #pasos .nav-pills{
        flex-direction:column; align-items:center; flex-wrap:nowrap;
      }
      #pasos .nav-pills>li>a{
        width:100%; max-width:280px; min-width:240px;
      }
    }

    /* ====== Botón outline rojo (misma forma/tamaño que tus .btn) ====== */
    .btn-gob-outline{
      background:#fff !important;
      color:var(--gob-rojo) !important;
      border:2px solid var(--gob-rojo) !important;
      box-shadow:none !important;
      text-decoration:none !important;
    }
    .btn-gob-outline:hover,
    .btn-gob-outline:focus{
      background:var(--gob-rojo) !important;
      color:#fff !important;
      border-color:var(--gob-rojo) !important;
      text-decoration:none !important;
      box-shadow:none !important;
    }

    /* ====== Acciones: apilar botones solo en móvil ====== */
    @media (max-width:575px){
      .nav-actions .btn{ width:100%; display:inline-block; }
      .nav-actions .col-xs-6{ width:100%; float:none; }
      .nav-actions .col-xs-6 + .col-xs-6{ margin-top:10px; text-align:center; }
    }

    /* ====== Select ====== */
    #tramite .form-control{
      width:100%; padding-right:40px; background-color:#fff; color:#333; font-weight:500;
    }
    #tramite .form-control:focus{ border-color:#ccc !important; box-shadow:none !important; outline:0; color:#333; }
    #tramite select{
      -webkit-appearance:none; -moz-appearance:none; appearance:none;
      background-image:url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'><path d='M1 1l5 5 5-5' fill='none' stroke='%23777' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'/></svg>");
      background-repeat:no-repeat; background-position:right 14px center; background-size:12px 8px;
    }
    #tramite select::-ms-expand{ display:none; }
    #tramite select option[disabled]{ color:#555; }
  </style>

  <script>
    // Tu script para los estilos del header/footer de GOB.MX se queda igual
    document.addEventListener('DOMContentLoaded', function(){
      setTimeout(function(){
        document.querySelectorAll('.navbar-toggle .icon-bar, .gm-navbar .icon-bar')
          .forEach(bar => { bar.style.backgroundColor = '#fff'; bar.style.borderColor = '#fff'; });
        document.querySelectorAll('.navbar-toggle, .gm-navbar .navbar-toggle')
          .forEach(btn => { btn.style.borderColor = 'rgba(255,255,255,0.5)'; });
        document.querySelectorAll('footer a, .gm-footer a')
          .forEach(link => { link.style.color = '#fff'; });
      }, 500);
    });
  </script>

  {{-- Breadcrumb --}}
  <ol class="breadcrumb" style="margin-top:10px">
    <li><a href="{{ url('/') }}">Inicio</a></li>
    <li>{{ $dependencia->nombre }}</li>
  </ol>

  {{-- Contenedor vacío donde se mostrará la alerta --}}
  <div id="alert-placeholder" style="margin-top: 15px;"></div>

  {{-- Título --}}
  <center><h1 style="margin:10px 0 6px;">{{ $dependencia->nombre }}</h1></center>
  <br>

  {{-- Pasos --}}
  <div id="pasos" class="text-center" style="margin-bottom:20px">
    <ul class="nav nav-pills">
      <li class="active">
        <a href="#" aria-current="step"><strong>Paso 1</strong><small>Selección del trámite</small></a>
      </li>
      <li>
        <a href="#"><strong>Paso 2</strong><small>Información de la persona</small></a>
      </li>
      <li>
        <a href="#"><strong>Paso 3</strong><small>Formato de pago</small></a>
      </li>
    </ul>
  </div>

  {{-- Sección de selección --}}
  <h3 style="margin-top:0">Selección del trámite:</h3>
  <div style="height:4px; width:48px; background:#a57f2c; margin:6px 0 18px;"></div>
  <p>Identifique y seleccione el trámite y/o servicio.</p>

  <form id="tramiteForm" action="{{ route('persona.store') }}" method="POST">
    @csrf
    <div id="tramite" style="margin-bottom:15px">
      <select class="form-control" id="tramiteSelect" name="tramite_id">
        <option value="" selected disabled>Selecciona un trámite</option>
        
        @foreach ($tramites as $tramite)
          <option 
            value="{{ $tramite->id }}" 
            data-descripcion="{{ $tramite->descripcion }}" 
            data-cuota="{{ $tramite->cuota }}"
            data-iva="{{ $tramite->iva }}">
            {{ $tramite->descripcion }} - ${{ number_format($tramite->cuota, 2) }} MXN
          </option>
        @endforeach

      </select>
    </div>

    {{-- Tabla que se llenará dinámicamente --}}
    <div class="table-responsive">
      <table class="table">
        <thead>
          <tr>
            <th>Descripción del concepto</th>
            <th>Importe en pesos M.N.</th>
            <th>IVA</th>
          </tr>
        </thead>
        <tbody id="tramiteDetalleBody"></tbody>
      </table>
    </div>

    {{-- Acciones --}}
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
  const tramiteTableBody = document.getElementById('tramiteDetalleBody');
  const alertPlaceholder = document.getElementById('alert-placeholder');

  // --- LÓGICA PARA LLENAR LA TABLA (sin cambios) ---
  tramiteSelect.addEventListener('change', function () {
    tramiteTableBody.innerHTML = '';
    alertPlaceholder.innerHTML = '';
    const selectedOption = this.options[this.selectedIndex];
    if (selectedOption.value) {
      const descripcion = selectedOption.getAttribute('data-descripcion');
      const cuota = parseFloat(selectedOption.getAttribute('data-cuota'));
      const iva = selectedOption.getAttribute('data-iva');
      const formattedCuota = new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(cuota);
      const ivaText = (iva == '1') ? 'IVA 16%' : 'IVA 0%';
      const newRow = `
        <tr>
          <td>${descripcion}</td>
          <td>${formattedCuota}</td>
          <td>${ivaText}</td>
        </tr>
      `;
      tramiteTableBody.innerHTML = newRow;
    }
  });

  // --- LÓGICA DE VALIDACIÓN (CON LA CORRECCIÓN PARA EL BOTÓN 'x') ---
  tramiteForm.addEventListener('submit', function (event) {
    if (!tramiteSelect.value) {
      event.preventDefault();
      const alertHTML = `
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          <strong>¡Precaución!</strong> Debes seleccionar un trámite para poder continuar.
        </div>
      `;
      alertPlaceholder.innerHTML = alertHTML;
      alertPlaceholder.scrollIntoView({ behavior: 'smooth', block: 'center' });

      // LÓGICA AÑADIDA PARA HACER FUNCIONAR EL BOTÓN 'x'
      const closeButton = alertPlaceholder.querySelector('.close');
      if (closeButton) {
        closeButton.addEventListener('click', function() {
          const alertElement = this.closest('.alert');
          if (alertElement) {
            alertElement.remove();
          }
        });
      }
    }
  });
});
</script>
@endpush