<section id="salir-section" class="content-card data-section">
  <h3 class="title-content">Cerrar sesión</h3>
  <hr>
  <form method="POST" action="{{ route('logout') }}"> @csrf
    <button type="submit" class="btn btn-danger">Cerrar sesión</button>
  </form>
</section>
