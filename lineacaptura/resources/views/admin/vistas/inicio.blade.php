<section id="inicio-section" class="content-card">
  <h2 class="title-content">Bienvenido, {{ Auth::user()->name }}</h2>
  <hr>
  <div class="user-block">
    <p><strong>Usuario:</strong> {{ Auth::user()->name }}</p>
    <p><strong>Email:</strong> {{ Auth::user()->email }}</p>
    <p><strong>Último acceso:</strong> {{ Auth::user()->updated_at ? Auth::user()->updated_at->format('d/m/Y H:i:s') : 'N/A' }}</p>
  </div>
  <hr>
  <p>Centro de control del sistema de líneas de captura del IMT.</p>

  <div class="kpi-grid">
    <div class="kpi"><p>Dependencias</p><p class="kpi-num">{{ $dependencias->count() }}</p></div>
    <div class="kpi"><p>Trámites</p><p class="kpi-num">{{ $tramites->count() }}</p></div>
    <div class="kpi"><p>Líneas de captura</p><p class="kpi-num">{{ $lineasCapturadas->count() }}</p></div>
    <div class="kpi"><p>Usuarios</p><p class="kpi-num">{{ $users->count() }}</p></div>
  </div>
</section>
