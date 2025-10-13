<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración - IMT</title>
    
    <style>
    /* ================================================================== */
    /* ESTILOS COMBINADOS                                                 */
    /* ================================================================== */

    /* Contenido de: estilovistas.css y estiloformularios.css (Encabezado) */
    .responsive-header {
        width: 100%;
        height: 60px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 10px;
        background-color: #03658C;
        border-bottom: 1px solid #ddd;
        flex-wrap: nowrap;
        position: fixed;
        z-index: 9999;
        top: 0;
        left: 0;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }
    .header-icon {
        width: 48px;
        height: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
        vertical-align: middle;
        flex-shrink: 0;
    }
    .header-icon img {
        max-width: 100%;
        max-height: 100%;
    }
    .header-title {
        flex: 1;
        text-align: center;
        font-size: 1.5rem;
        margin: 0 10px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        order: 2;
        color: #ffffff;
    }
    .left-icon{
        width: 55px;
        height: 55px;
        order: 1;
    }

    @media (max-width: 900px) {
        .header-title { font-size: 1.6rem; }
        .header-icon { width: 40px; height: 40px; }
        .responsive-header { padding: 8px 16px; }
    }
    @media (max-width: 600px) {
        .header-title { font-size: 1.3rem; }
        .header-icon { width: 32px; height: 32px; }
        .responsive-header { padding: 6px 12px; }
    }
    @media (max-width: 400px) {
        .left-icon, .user-title, .space-title, .right-icon { display: none; }
        .header-title { font-size: 1.2rem; margin: 0; }
    }

    /* Contenido de: estilovistas.css y estiloformularios.css (Cuerpo y Formularios) */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }
    body {
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
        background: ghostwhite;
    }
    a {
        text-decoration: none;
    }
    .sidebar {
        position: fixed;
        height: 100vh;
        background-color: #03658C;
        padding-top: 80px;
        transition: all 0.3s ease-in-out;
        width: 220px;
    }
    .sidebar ul { list-style: none; }
    .sidebar li { margin: 0px 0; }
    .sidebar a {
        color: #ffffff;
        text-decoration: none;
        padding: 10px 20px;
        margin: 1px 0px;
        display: flex;
        font-size: .8em;
        gap: 8px;
        align-items: center;
    }
    .sidebar a img{ width: 20px; height: 20px; }
    .sidebar a:hover { background-color: #007bac; }
    .scrollable {
        width: 100%;
        height: 100%;
        overflow: auto;
        scrollbar-color: #ffffff #ffffff00;
    }
    .separador-side{
        border: 1px solid #ffffff;
        width:70%;
        margin-left:15%;
        margin-right:15%;
    }
    .contenido {
        margin-left: 220px;
        padding: 50px;
        padding-top: 80px;
        color: #5a5c69;
    }
    .content-info{
        background: #ffffff;
        padding: 20px;
        box-shadow: 4px 4px 10px rgba(0, 0, 0, 0.3);
        border-radius: 10px;
    }
    .title-content {
        font-size: 1.5rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        font-weight: bold;
    }
    .div-fila {
        display: flex;
        align-items: center;
        gap: 20px;
    }
    .div-fila > * { flex: 1; }

    /* Contenido de: estilosBotones.css */
    .btn-success, .btn-danger, .btn-primary, .btn-warning {
        display: inline-flex;
        align-items: center;
        padding: 8px 12px;
        border: none;
        color: white;
        font-size: .8em;
        cursor: pointer;
        border-radius: 5px;
        font-weight: 600;
        gap: 8px;
        text-decoration: none;
        white-space: nowrap;
    }
    .btn-success { background-color: #1cc88a; }
    .btn-success:hover { background: #18af78; }
    .btn-danger { background-color: #e74a3b; }
    .btn-danger:hover { background: #c74033; }
    .btn-primary { background-color: #1EA4D9; }
    .btn-primary:hover { background: #03658C; }
    .btn-warning { background-color: #f4a100; }
    .btn-warning:hover { background: #dc9300; }
    .btn-success img, .btn-danger img, .btn-primary img, .btn-warning img {
        width: 20px;
        height: 20px;
        object-fit: contain;
    }
    </style>
</head>
<body>

    <div id="admin-panel">
        <header class="responsive-header">
            <div class="header-icon left-icon">
                <img src="{{ asset('img/imtblanco.png') }}" alt="Logo IMT">
            </div>
            <h1 class="header-title">Administración de Líneas de Captura</h1>
        </header>

        <aside class="sidebar">
            <ul class="scrollable">
                <li><a href="#"><img src="https://img.icons8.com/ios-filled/50/ffffff/dashboard-layout.png" alt="dashboard"/><span>Dashboard</span></a></li>
                <li><a href="#"><img src="https://img.icons8.com/ios-filled/50/ffffff/document.png" alt="tramites"/><span>Trámites</span></a></li>
                <li><a href="#"><img src="https://img.icons8.com/ios-filled/50/ffffff/building.png" alt="dependencias"/><span>Dependencias</span></a></li>

                {{-- ========================================================== --}}
                {{-- INICIO DE LA CORRECCIÓN                                    --}}
                {{-- Se agregó el enlace a la página de registro de usuarios.   --}}
                {{-- ========================================================== --}}
                <li><a href="{{ route('register') }}"><img src="https://img.icons8.com/ios-filled/50/ffffff/add-user-male.png" alt="usuarios"/><span>Usuarios</span></a></li>
                {{-- ========================================================== --}}
                {{-- FIN DE LA CORRECCIÓN                                       --}}
                {{-- ========================================================== --}}

                <hr class="separador-side">
                
                {{-- CÓDIGO ACTUALIZADO PARA CERRAR SESIÓN --}}
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <a href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();">
                            <img src="https://img.icons8.com/ios-filled/50/ffffff/logout-rounded-left.png" alt="salir"/>
                            <span>Salir</span>
                        </a>
                    </form>
                </li>
            </ul>
        </aside>

        <main class="contenido">
            <div class="content-info">
                {{-- Mensaje de bienvenida con el nombre del usuario autenticado --}}
                <h2 class="title-content">Bienvenido, {{ Auth::user()->name }}</h2>
                <hr>
                <p style="margin-top: 20px;">Desde este panel podrás gestionar los trámites, dependencias y configuraciones del sistema de líneas de captura.</p>
                
                <div class="div-fila" style="margin-top: 30px;">
                    <button class="btn-primary">
                        <img src="https://img.icons8.com/ios-glyphs/30/ffffff/plus-math.png" alt="nuevo"/>
                        Nuevo Trámite
                    </button>
                    <button class="btn-warning">
                        <img src="https://img.icons8.com/ios-glyphs/30/ffffff/pencil.png" alt="editar"/>
                        Editar Dependencia
                    </button>
                    <button class="btn-danger">
                        <img src="https://img.icons8.com/ios-glyphs/30/ffffff/trash.png" alt="eliminar"/>
                        Eliminar Usuario
                    </button>
                </div>
            </div>
        </main>
    </div>

</body>
</html>