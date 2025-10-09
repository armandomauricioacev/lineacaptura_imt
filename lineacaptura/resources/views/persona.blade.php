@extends('layouts.base')

@section('title', 'Datos de la persona')

@section('content')
  <style>
    /* ... (Tu CSS no necesita cambios) ... */
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
    .btn-gob-outline:focus{ background:var(--gob-rojo) !important; color:#fff !important; border-color:var(--gob-rojo) !important; box-shadow:none !important; text-decoration:none !important; outline: none !important; }
    .error-message { display: none; color: #a94442; margin-top: 5px; font-size: 12px; }
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
    <li><a href="{{ url('/tramite') }}">Instituto Mexicano del Transporte</a></li>
  </ol>

  {{-- Contenedor para la alerta --}}
  <div id="alert-placeholder" style="margin-top: 15px;"></div>

  {{-- Título --}}
  <center><h1 style="margin:10px 0 6px;">Instituto Mexicano del Transporte</h1></center>
  <br>

  {{-- Pasos (Paso 2 activo) --}}
  <div id="pasos" class="text-center" style="margin-bottom:20px">
    <ul class="nav nav-pills">
        <li><a href="#"><strong>Paso 1</strong><small>Selección del trámite</small></a></li>
        <li class="active"><a href="#" aria-current="step"><strong>Paso 2</strong><small>Información de la persona</small></a></li>
        <li><a href="#"><strong>Paso 3</strong><small>Formato de pago</small></a></li>
    </ul>
  </div>

  {{-- Formulario --}}
  <h3 style="margin-top:0">Información de la persona:</h3>
  <div style="height:4px; width:48px; background:#a57f2c; margin:6px 0 18px;"></div>

  <form id="personaForm" action="{{ route('pago.store') }}" method="POST" role="form" aria-label="Formulario de datos de la persona" novalidate>
    @csrf
    {{-- Tipo de persona --}}
    <div class="form-group">
      <label class="control-label">Tipo de persona <span style="color:#a00">*</span></label>
      <div>
        <label class="radio-inline">
          <input type="radio" id="tipo_persona_fisica" name="tipo_persona" value="fisica" required> Persona física
        </label>
        <label class="radio-inline" style="margin-left:16px">
          <input type="radio" id="tipo_persona_moral" name="tipo_persona" value="moral" required> Persona moral
        </label>
      </div>
      <p class="help-block" style="margin-top:6px">Selecciona una opción para mostrar los campos.</p>
    </div>

    {{-- Persona física (oculto por defecto) --}}
    <div id="pf" style="display:none; margin-top:10px">
      <div class="row">
        <div class="col-sm-6">
          <div class="form-group">
            <label for="curp">CURP <span style="color:#a00">*</span></label>
            <input type="text" id="curp" name="curp" class="form-control to-uppercase" placeholder="Ingresa tu CURP"
                   maxlength="18" pattern="[A-Z]{4}[0-9]{6}[H,M][A-Z]{5}[A-Z0-9]{1}[0-9]{1}" title="El CURP debe tener 18 caracteres en el formato oficial.">
            <small id="curp-error" class="error-message"></small>
          </div>
        </div>
        <div class="col-sm-6">
          <div class="form-group">
            <label for="rfc_pf">RFC <span style="color:#a00">*</span></label>
            <input type="text" id="rfc_pf" name="rfc" class="form-control to-uppercase" placeholder="Ingresa tu RFC"
                   maxlength="13" pattern="[A-Z&Ñ]{4}[0-9]{6}([A-Z0-9]){3}" title="El RFC para persona física debe tener 13 caracteres.">
            <small id="rfc_pf-error" class="error-message"></small>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-sm-4">
          <div class="form-group">
            <label for="nombres">Nombre(s) <span style="color:#a00">*</span></label>
            <input type="text" id="nombres" name="nombres" class="form-control to-uppercase" placeholder="Ingresa tu nombre(s)">
            <small id="nombres-error" class="error-message"></small>
          </div>
        </div>
        <div class="col-sm-4">
          <div class="form-group">
            <label for="apellido_paterno">Apellido paterno <span style="color:#a00">*</span></label>
            <input type="text" id="apellido_paterno" name="apellido_paterno" class="form-control to-uppercase" placeholder="Ingresa tu primer apellido">
            <small id="apellido_paterno-error" class="error-message"></small>
          </div>
        </div>
        <div class="col-sm-4">
          <div class="form-group">
            <label for="apellido_materno">Apellido materno <span style="color:#a00">*</span></label>
            <input type="text" id="apellido_materno" name="apellido_materno" class="form-control to-uppercase" placeholder="Ingresa tu segundo apellido">
            <small id="apellido_materno-error" class="error-message"></small>
          </div>
        </div>
      </div>
    </div>

    {{-- Persona moral (oculto por defecto) --}}
    <div id="pm" style="display:none; margin-top:10px">
      <div class="row">
        <div class="col-sm-6">
          <div class="form-group">
            <label for="rfc_pm">RFC <span style="color:#a00">*</span></label>
            <input type="text" id="rfc_pm" name="rfc_moral" class="form-control to-uppercase" placeholder="Ingresa el RFC de la empresa"
                   maxlength="12" pattern="[A-Z&Ñ]{3}[0-9]{6}([A-Z0-9]){3}" title="El RFC para persona moral debe tener 12 caracteres.">
            <small id="rfc_pm-error" class="error-message"></small>
          </div>
        </div>
        <div class="col-sm-6">
          <div class="form-group">
            <label for="razon_social">Razón social <span style="color:#a00">*</span></label>
            <input type="text" id="razon_social" name="razon_social" class="form-control to-uppercase" placeholder="Nombre de la empresa">
            <small id="razon_social-error" class="error-message"></small>
          </div>
        </div>
      </div>
    </div>

    {{-- Navegación --}}
    <div class="row nav-actions" style="margin-top:10px">
      <div class="col-xs-6">
        <a href="{{ url('/tramite') }}" class="btn btn-gob-outline" aria-label="Regresar al paso anterior">Regresar</a>
      </div>
      <div class="col-xs-6 text-right">
        <button type="submit" class="btn btn-gob-outline" id="btn-continuar" aria-label="Continuar al formato de pago">Siguiente</button>
      </div>
    </div>
  </form>

  <p style="margin-top:15px; color:#777; font-size:12px">* Campos obligatorios</p>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const personaForm = document.getElementById('personaForm');
    const pf = document.getElementById('pf');
    const pm = document.getElementById('pm');
    const radios = document.querySelectorAll('input[name="tipo_persona"]');
    const uppercaseInputs = document.querySelectorAll('.to-uppercase');
    const alertPlaceholder = document.getElementById('alert-placeholder');
    
    // --- Lógica para mostrar/ocultar campos (sin cambios) ---
    function syncTipo() {
        const r = document.querySelector('input[name="tipo_persona"]:checked');
        pf.querySelectorAll('input').forEach(input => input.required = false);
        pm.querySelectorAll('input').forEach(input => input.required = false);
        
        if (r) {
            if (r.value === 'fisica') {
                pm.style.display = 'none';
                pf.style.display = 'block';
                pf.querySelectorAll('input').forEach(input => input.required = true);
            } else {
                pf.style.display = 'none';
                pm.style.display = 'block';
                pm.querySelectorAll('input').forEach(input => input.required = true);
            }
        } else {
            pf.style.display = 'none';
            pm.style.display = 'none';
        }
        
        alertPlaceholder.innerHTML = '';
        document.querySelectorAll('.error-message').forEach(el => el.style.display = 'none');
    }
    radios.forEach(r => r.addEventListener('change', syncTipo));
    syncTipo();
    
    // --- Lógica de mayúsculas (sin cambios) ---
    uppercaseInputs.forEach(input => {
        input.addEventListener('input', function() {
            let originalValue = this.value;
            let transformedValue = originalValue.toUpperCase();
            if (this.id === 'curp' || this.id === 'rfc_pf' || this.id === 'rfc_pm') {
                transformedValue = transformedValue.replace(/[^A-Z0-9Ñ&]/g, '');
            }
            if (originalValue !== transformedValue) {
                this.value = transformedValue;
            }
        });
    });

    // --- Lógica de validación de campos (sin cambios) ---
    function showError(elementId, message) {
        const errorElement = document.getElementById(elementId + '-error');
        if (errorElement) {
            errorElement.textContent = message;
            errorElement.style.display = 'block';
        }
    }

    function hideError(elementId) {
        const errorElement = document.getElementById(elementId + '-error');
        if (errorElement) {
            errorElement.textContent = '';
            errorElement.style.display = 'none';
        }
    }
    
    // --- Lógica principal del formulario al enviar ---
    personaForm.addEventListener('submit', function(event) {
        event.preventDefault(); // Siempre prevenimos el envío para validar
        
        let isValid = true;
        let firstErrorElement = null;

        alertPlaceholder.innerHTML = ''; // Limpiar alerta grande
        document.querySelectorAll('.error-message').forEach(el => el.style.display = 'none'); // Limpiar errores pequeños

        const tipoPersonaSeleccionado = document.querySelector('input[name="tipo_persona"]:checked');
        
        if (!tipoPersonaSeleccionado) {
            isValid = false;
            const alertHTML = `
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                  <strong>¡Atención!</strong> Debes seleccionar un tipo de persona para poder continuar.
                </div>
            `;
            alertPlaceholder.innerHTML = alertHTML;
            
            // ==========================================================
            // CAMBIO CLAVE: Hacer scroll a la alerta, no al campo.
            // ==========================================================
            alertPlaceholder.scrollIntoView({ behavior: 'smooth', block: 'center' });

            const closeButton = alertPlaceholder.querySelector('.close');
            if (closeButton) {
                closeButton.addEventListener('click', function() { this.closest('.alert').remove(); });
            }

        } else {
            const visibleSection = (tipoPersonaSeleccionado.value === 'fisica') ? pf : pm;
            const requiredInputs = visibleSection.querySelectorAll('input[required]');
            for (const input of requiredInputs) {
                let hasError = false;
                if (!input.value.trim()) {
                    showError(input.id, 'Este campo es obligatorio.');
                    hasError = true;
                } else if (input.pattern && !new RegExp('^' + input.pattern + '$').test(input.value)) {
                    showError(input.id, input.title || 'El formato no es válido.');
                    hasError = true;
                }
                if (hasError) {
                    isValid = false;
                    if (!firstErrorElement) firstErrorElement = input;
                }
            }
            
            // Si hay errores en los campos, hacer scroll al primero de ellos
            if (!isValid && firstErrorElement) {
                firstErrorElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        }
        
        // Si después de todas las validaciones, todo es correcto, enviar el formulario
        if (isValid) {
            personaForm.submit();
        }
    });

    // Validar en tiempo real al salir de un campo (sin cambios)
    document.querySelectorAll('#pf input[required], #pm input[required]').forEach(input => {
        input.addEventListener('blur', function() {
            if (!this.value.trim()) {
                showError(this.id, 'Este campo es obligatorio.');
            } else if (this.pattern && !new RegExp('^' + this.pattern + '$').test(this.value)) {
                showError(this.id, this.title || 'El formato no es válido.');
            } else {
                hideError(this.id);
            }
        });
    });
});
</script>
@endpush