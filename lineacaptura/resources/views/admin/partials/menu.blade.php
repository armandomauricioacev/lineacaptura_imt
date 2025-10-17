<aside class="sidebar">
  <ul id="admin-menu">
    <li><a href="#" data-target="inicio-section"            class="nav-link active"><img src="https://img.icons8.com/ios-glyphs/30/ffffff/home.png"/><span>Inicio</span></a></li>
    <li><a href="#" data-target="dependencias-section"      class="nav-link"><img src="https://img.icons8.com/ios-filled/50/ffffff/building.png"/><span>Dependencias</span></a></li>
    <li><a href="#" data-target="tramites-section"          class="nav-link"><img src="https://img.icons8.com/ios-filled/50/ffffff/document.png"/><span>Trámites</span></a></li>
    <li><a href="#" data-target="lineas-captura-section"    class="nav-link"><img src="https://img.icons8.com/ios-filled/50/ffffff/receipt.png"/><span>Líneas de captura</span></a></li>
    <li><a href="#" data-target="users-section"             class="nav-link"><img src="https://img.icons8.com/ios-filled/50/ffffff/user.png"/><span>Usuarios</span></a></li>
    <hr class="separador-side">
    <li><a href="{{ route('register') }}"><img src="https://img.icons8.com/ios-filled/50/ffffff/add-user-male.png"/><span>Nuevo administrador</span></a></li>
    <li>
      <form method="POST" action="{{ route('logout') }}"> @csrf
        <a href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();">
          <img src="https://img.icons8.com/ios-filled/50/ffffff/logout-rounded-left.png"/><span>Salir</span>
        </a>
      </form>
    </li>
  </ul>
</aside>
