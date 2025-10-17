<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Panel de Administración - IMT</title>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head>
<body>
  <div id="admin-panel">
    @include('admin.partials.header')
    @include('admin.partials.menu')

    <main class="contenido">
      @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
      @if(session('error'))   <div class="alert alert-error">{{ session('error') }}</div>   @endif

      {{-- Secciones (solo una visible, las otras ocultas por CSS/JS) --}}
      @include('admin.vistas.inicio',        ['dependencias'=>$dependencias,'tramites'=>$tramites,'lineasCapturadas'=>$lineasCapturadas,'users'=>$users])
      @include('admin.vistas.dependencias',  ['dependencias'=>$dependencias])
      @include('admin.vistas.tramites',      ['tramites'=>$tramites])
      @include('admin.vistas.lineas',        ['lineasCapturadas'=>$lineasCapturadas])
      @include('admin.vistas.usuarios',      ['users'=>$users])
      @include('admin.vistas.nuevoadmin')
      @include('admin.vistas.salir')
    </main>
  </div>

  {{-- JS base mínimo (navegación, helpers) --}}
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const links = document.querySelectorAll('.sidebar .nav-link');
      const sections = document.querySelectorAll('.content-card');
      const show = id => sections.forEach(s => s.style.display = (s.id === id) ? 'block' : 'none');
      show('inicio-section');
      links.forEach(l => l.addEventListener('click', e => {
        e.preventDefault();
        links.forEach(x => x.classList.remove('active')); l.classList.add('active');
        show(l.getAttribute('data-target'));
      }));
      setTimeout(() => document.querySelectorAll('.alert').forEach(a => a.style.display='none'), 5000);
    });
    function openModal(id){ document.getElementById(id).classList.add('show'); }
    function closeModal(id){ document.getElementById(id).classList.remove('show'); }
    window.addEventListener('click', e => { if (e.target.classList?.contains('modal')) e.target.classList.remove('show'); });
    function toggleClearButton(btnId, v){ const b=document.getElementById(btnId); if(b) b.style.display = v ? 'block':'none'; }
  </script>
</body>
</html>
