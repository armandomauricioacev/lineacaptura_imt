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
        /* CAMBIO 1: Color de fondo del header a azul */
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
        cursor: pointer;
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
        /* CAMBIO 2: Color del texto del título a blanco */
        color: #ffffff;
    }
    .btn-logout {
        background: transparent;
        border: none;
        cursor: pointer;
        border-radius: 25px;
    }
    .left-icon{
        width: 55px;
        height: 55px;
        order: 1;
    }
    .right-icon {
        height: 40px;
        width: 40px;
        order: 5;
    }
    .btn-logout:hover {
        background: rgb(195, 11, 11);
        border: none;
        cursor: pointer;
        padding: 4px;
        transition:all 0.3s ease-in-out;
    }
    .right-icon:hover img {
        background: rgb(195, 11, 11);
        border-radius: 25px;
        padding: 2px;
        height: 30px;
        width: 30px;
        content: url('/img/logout-bl.png');
    }
    .user-title{ order: 3; }
    .space-title{ order: 4; }

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
    footer {
        color: #5C5C69;
        font-size: .8em;
        text-align: center;
        padding: 10px 0;
        background-color: #FFFFFF;
        border-top: 1px solid #ddd;
        position: fixed;
        bottom: 0;
        left: 0;
        width: 100%;
        box-shadow: 0 -2px 5px rgba(0, 0, 0, 0.1);
        z-index: -1;
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
    
    /* Contenido de: estilosForm.css */
    label {
        font-weight: bold;
        display: block;
        margin-top: 15px;
    }
    select, input[type="text"], input[type="number"], input[type="email"], input[type="date"], input[type="password"] {
        width: 100%;
        padding: 8px;
        margin-top: 5px;
        border: 1px solid #ccc;
        border-radius: 4px;
        transition: border-color 0.3s;
    }
    select:focus, input:focus {
        border-color: blue;
        outline: none;
    }
    ::placeholder { color: #a9a9a9; }
    
    /* Contenido de: estilosTablas.css */
    .responsive-table {
        width: 100%;
        border-collapse: collapse;
        table-layout: auto;
        word-wrap: break-word;
    }
    .responsive-table th, .responsive-table td {
        border: 1px solid #E3E6F0;
        padding: 8px;
        text-align: left;
        word-break: break-word;
        overflow-wrap: break-word;
    }
    .responsive-table th {
        background-color: #f4f4f4;
        font-weight: bold;
    }
    
    /* Estilos adicionales para la página de login */
    #login-screen {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        background-color: #f8f9fc;
    }
    .login-box {
        width: 100%;
        max-width: 400px;
        padding: 40px;
        background: #fff;
        box-shadow: 0 15px 25px rgba(0,0,0,.6);
        border-radius: 10px;
        text-align: center;
    }
    .login-box img {
        width: 100px;
        margin-bottom: 20px;
    }
    .login-box h2 {
        margin: 0 0 30px;
        padding: 0;
        color: #333;
        text-align: center;
    }
    #admin-panel {
        display: none; /* Oculto por defecto */
    }
    #error-message {
        color: #e74a3b;
        margin-top: 15px;
        display: none;
    }
    </style>
</head>
<body>

    <div id="login-screen">
        <div class="login-box">
            <img src="{{ asset('img/imt.png') }}" alt="Logo IMT">
            <h2>Panel de Administración</h2>
            <form id="login-form">
                <div class="form-group">
                    <label for="username" style="text-align: left;">Usuario</label>
                    <input type="text" id="username" name="username" required placeholder="Ingresa tu usuario">
                </div>
                <div class="form-group">
                    <label for="password" style="text-align: left;">Contraseña</label>
                    <input type="password" id="password" name="password" required placeholder="Ingresa tu contraseña">
                </div>
                <button type="submit" class="btn-primary" style="width: 100%; display: block; margin-top: 20px; justify-content: center;">Entrar</button>
                <p id="error-message">Usuario o contraseña incorrectos.</p>
            </form>
        </div>
    </div>

    <div id="admin-panel">
        <header class="responsive-header">
            <div class="header-icon left-icon">
                {{-- CAMBIO 3: Usar la nueva imagen del logo en blanco --}}
                <img src="{{ asset('img/imtblanco.png') }}" alt="Logo IMT">
            </div>
            <h1 class="header-title">Administración de Líneas de Captura</h1>
        </header>

        <aside class="sidebar">
            <ul class="scrollable">
                <li><a href="#"><img src="https://img.icons8.com/ios-filled/50/ffffff/dashboard-layout.png" alt="dashboard"/><span>Dashboard</span></a></li>
                <li><a href="#"><img src="https://img.icons8.com/ios-filled/50/ffffff/document.png" alt="tramites"/><span>Trámites</span></a></li>
                <li><a href="#"><img src="https://img.icons8.com/ios-filled/50/ffffff/building.png" alt="dependencias"/><span>Dependencias</span></a></li>
                <hr class="separador-side">
                <li><a href="#" id="logout-button"><img src="https://img.icons8.com/ios-filled/50/ffffff/logout-rounded-left.png" alt="salir"/><span>Salir</span></a></li>
            </ul>
        </aside>

        <main class="contenido">
            <div class="content-info">
                <h2 class="title-content">Bienvenido, Administrador</h2>
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const loginForm = document.getElementById('login-form');
            const loginScreen = document.getElementById('login-screen');
            const adminPanel = document.getElementById('admin-panel');
            const errorMessage = document.getElementById('error-message');
            const logoutButton = document.getElementById('logout-button');

            loginForm.addEventListener('submit', function(event) {
                event.preventDefault();
                
                const username = document.getElementById('username').value;
                const password = document.getElementById('password').value;

                if (username === 'root' && password === 'root') {
                    loginScreen.style.display = 'none';
                    adminPanel.style.display = 'block';
                    errorMessage.style.display = 'none';
                } else {
                    errorMessage.style.display = 'block';
                }
            });

            logoutButton.addEventListener('click', function(event) {
                event.preventDefault();
                adminPanel.style.display = 'none';
                loginScreen.style.display = 'flex';
                document.getElementById('username').value = '';
                document.getElementById('password').value = '';
            });
        });
    </script>

</body>
</html>