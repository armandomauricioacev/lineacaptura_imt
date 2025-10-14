<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Panel de Administración - IMT</title>
    
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html { scroll-behavior: smooth; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif; background-color: #f8f9fc; color: #5a5c69; }
        a { text-decoration: none; cursor: pointer; }
        .responsive-header { width: 100%; height: 60px; display: flex; align-items: center; justify-content: space-between; padding: 0 20px; background-color: #03658C; position: fixed; z-index: 1000; top: 0; left: 0; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .header-icon { height: 40px; }
        .header-icon img { max-height: 100%; }
        .header-title { flex: 1; text-align: center; font-size: 1.4rem; color: #ffffff; font-weight: 500; margin: 0 20px; }
        .sidebar { position: fixed; top: 60px; left: 0; height: calc(100vh - 60px); background-color: #03658C; padding-top: 20px; width: 240px; z-index: 999; overflow-y: auto; }
        .sidebar ul { list-style: none; padding: 0; }
        .sidebar a { color: #ffffff; padding: 12px 20px; display: flex; align-items: center; gap: 12px; font-size: 0.9em; transition: all 0.2s ease-in-out; }
        .sidebar a.active, .sidebar a:hover { background-color: #007bac; padding-left: 25px; }
        .sidebar a img { width: 20px; height: 20px; opacity: 0.8; }
        .sidebar .separador-side { border: 0; border-top: 1px solid rgba(255, 255, 255, 0.2); margin: 15px auto; width: 80%; }
        .contenido { margin-left: 240px; padding: 20px; padding-top: 80px; }
        .content-card { background: #ffffff; padding: 25px; box-shadow: 0 0 15px rgba(0, 0, 0, 0.07); border-radius: 10px; margin-bottom: 25px; }
        .data-section { display: none; }
        .title-content { font-size: 1.8rem; font-weight: 600; color: #3a3b45; display: flex; justify-content: space-between; align-items: center; }
        hr { border: 0; border-top: 1px solid #e3e6f0; margin: 15px 0; }
        .table-container { overflow-x: auto; }
        .data-table { width: 100%; border-collapse: collapse; font-size: 0.85em; }
        .data-table th, .data-table td { padding: 10px 12px; border: 1px solid #e3e6f0; text-align: left; white-space: nowrap; }
        .data-table thead th { background-color: #f8f9fc; font-weight: 600; }
        .data-table tbody tr:hover { background-color: #e9ecef; }
        .data-table .wrap-text { white-space: normal; min-width: 250px; }
        
        /* Barra de búsqueda y filtros */
        .search-filter-container { 
            background: #f8f9fc; 
            padding: 15px; 
            border-radius: 8px; 
            margin-bottom: 20px; 
            display: flex;
            gap: 15px;
            align-items: center;
            flex-wrap: wrap;
        }
        .search-box {
            flex: 1;
            min-width: 300px;
            position: relative;
        }
        .search-box input {
            width: 100%;
            padding: 10px 40px 10px 15px;
            border: 1px solid #d1d3e2;
            border-radius: 6px;
            font-size: 0.9em;
        }
        .search-box input:focus {
            outline: none;
            border-color: #03658C;
            box-shadow: 0 0 0 3px rgba(3, 101, 140, 0.1);
        }
        .search-icon {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #858796;
            pointer-events: none;
        }
        .clear-search {
            position: absolute;
            right: 35px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #858796;
            cursor: pointer;
            font-size: 1.2em;
            padding: 0;
            width: 20px;
            height: 20px;
            display: none;
        }
        .clear-search:hover {
            color: #dc3545;
        }
        .filter-group {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        .filter-select {
            padding: 8px 12px;
            border: 1px solid #d1d3e2;
            border-radius: 6px;
            font-size: 0.9em;
            background: white;
        }
        .results-count {
            font-size: 0.85em;
            color: #6c757d;
            padding: 5px 10px;
            background: white;
            border-radius: 4px;
        }
        .no-results {
            text-align: center;
            padding: 40px;
            color: #858796;
            font-size: 1.1em;
        }
        .highlight {
            background-color: #fff3cd;
            padding: 2px 4px;
            border-radius: 3px;
        }
        
        /* Botones de acción */
        .btn { padding: 6px 12px; border: none; border-radius: 4px; cursor: pointer; font-size: 0.85em; transition: all 0.2s; }
        .btn-primary { background-color: #03658C; color: white; }
        .btn-primary:hover { background-color: #025070; }
        .btn-success { background-color: #28a745; color: white; }
        .btn-success:hover { background-color: #218838; }
        .btn-warning { background-color: #ffc107; color: #333; }
        .btn-warning:hover { background-color: #e0a800; }
        .btn-danger { background-color: #dc3545; color: white; }
        .btn-danger:hover { background-color: #c82333; }
        .btn-sm { padding: 4px 8px; font-size: 0.8em; }
        .action-buttons { display: flex; gap: 5px; }
        
        /* Modal */
        .modal { display: none; position: fixed; z-index: 2000; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.5); }
        .modal.show { display: flex; align-items: center; justify-content: center; }
        .modal-content { background-color: #fefefe; margin: auto; padding: 0; border-radius: 8px; width: 90%; max-width: 600px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); max-height: 90vh; overflow-y: auto; }
        .modal-header { padding: 20px; background-color: #03658C; color: white; border-radius: 8px 8px 0 0; display: flex; justify-content: space-between; align-items: center; }
        .modal-header h3 { margin: 0; font-size: 1.3rem; }
        .close { color: white; font-size: 28px; font-weight: bold; cursor: pointer; }
        .close:hover { opacity: 0.7; }
        .modal-body { padding: 20px; }
        .modal-footer { padding: 15px 20px; border-top: 1px solid #e3e6f0; display: flex; justify-content: flex-end; gap: 10px; }
        
        /* Formularios */
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: 500; color: #3a3b45; }
        .form-control { width: 100%; padding: 8px 12px; border: 1px solid #d1d3e2; border-radius: 4px; font-size: 0.9em; }
        .form-control:focus { outline: none; border-color: #03658C; }
        .form-check { display: flex; align-items: center; gap: 8px; }
        .form-check input[type="checkbox"] { width: 18px; height: 18px; cursor: pointer; }
        
        /* Alertas */
        .alert { padding: 12px 20px; border-radius: 4px; margin-bottom: 20px; }
        .alert-success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    </style>
</head>
<body>
    <div id="admin-panel">
        <header class="responsive-header">
            <div class="header-icon">
                <img src="{{ asset('img/imtblanco.png') }}" alt="Logo IMT">
            </div>
            <h1 class="header-title">Panel de Administración</h1>
        </header>

        <aside class="sidebar">
            <ul id="admin-menu">
                <li><a href="#" data-target="inicio-section" class="nav-link active"><img src="https://img.icons8.com/ios-glyphs/30/ffffff/home.png" alt="inicio"/><span>Inicio</span></a></li>
                <li><a href="#" data-target="dependencias-section" class="nav-link"><img src="https://img.icons8.com/ios-filled/50/ffffff/building.png" alt="dependencias"/><span>Dependencias</span></a></li>
                <li><a href="#" data-target="tramites-section" class="nav-link"><img src="https://img.icons8.com/ios-filled/50/ffffff/document.png" alt="tramites"/><span>Trámites</span></a></li>
                <li><a href="#" data-target="lineas-captura-section" class="nav-link"><img src="https://img.icons8.com/ios-filled/50/ffffff/receipt.png" alt="lineas-captura"/><span>Líneas de Captura</span></a></li>
                <li><a href="#" data-target="users-section" class="nav-link"><img src="https://img.icons8.com/ios-filled/50/ffffff/user.png" alt="usuarios"/><span>Usuarios</span></a></li>
                
                <hr class="separador-side">

                <li><a href="{{ route('register') }}"><img src="https://img.icons8.com/ios-filled/50/ffffff/add-user-male.png" alt="nuevo-administrador"/><span>Nuevo Administrador</span></a></li>
                
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
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            
            @if(session('error'))
                <div class="alert alert-error">{{ session('error') }}</div>
            @endif

            <section id="inicio-section" class="content-card">
                <h2 class="title-content">Bienvenido, {{ Auth::user()->name }}</h2>
                <hr>
                <div style="margin-bottom: 20px;">
                    <p><strong>Usuario:</strong> {{ Auth::user()->name }}</p>
                    <p><strong>Email:</strong> {{ Auth::user()->email }}</p>
                    <p><strong>Último acceso:</strong> {{ Auth::user()->updated_at ? Auth::user()->updated_at->format('d/m/Y H:i:s') : 'N/A' }}</p>
                </div>
                <hr>
                <p>Este es el centro de control para el sistema de líneas de captura del IMT. Desde aquí puedes gestionar toda la información del sistema.</p>
                
                <div style="margin-top: 30px; padding: 20px; background-color: #f8f9fc; border-radius: 8px;">
                    <h3 style="margin-bottom: 15px; color: #03658C;">Resumen del Sistema</h3>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
                        <div style="background: white; padding: 15px; border-radius: 6px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                            <p style="color: #6c757d; margin-bottom: 5px;">Dependencias</p>
                            <p style="font-size: 2em; font-weight: bold; color: #03658C;">{{ $dependencias->count() }}</p>
                        </div>
                        <div style="background: white; padding: 15px; border-radius: 6px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                            <p style="color: #6c757d; margin-bottom: 5px;">Trámites</p>
                            <p style="font-size: 2em; font-weight: bold; color: #28a745;">{{ $tramites->count() }}</p>
                        </div>
                        <div style="background: white; padding: 15px; border-radius: 6px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                            <p style="color: #6c757d; margin-bottom: 5px;">Líneas de Captura</p>
                            <p style="font-size: 2em; font-weight: bold; color: #ffc107;">{{ $lineasCapturadas->count() }}</p>
                        </div>
                        <div style="background: white; padding: 15px; border-radius: 6px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                            <p style="color: #6c757d; margin-bottom: 5px;">Usuarios</p>
                            <p style="font-size: 2em; font-weight: bold; color: #dc3545;">{{ $users->count() }}</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- DEPENDENCIAS -->
            <section id="dependencias-section" class="content-card data-section">
                <div class="title-content">
                    <h3>Dependencias</h3>
                    <button class="btn btn-primary" onclick="openCreateModal('dependencia')">+ Nueva Dependencia</button>
                </div>
                <hr>
                
                <!-- Búsqueda y Filtros -->
                <div class="search-filter-container">
                    <div class="search-box">
                        <input type="text" id="searchDependencias" placeholder="🔍 Buscar en dependencias (nombre, clave, unidad administrativa...)">
                        <button class="clear-search" id="clearSearchDep" onclick="clearSearch('dependencias')">&times;</button>
                        <span class="search-icon">🔍</span>
                    </div>
                    <span class="results-count" id="resultsDep">Mostrando {{ $dependencias->count() }} de {{ $dependencias->count() }}</span>
                </div>
                
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Clave</th>
                                <th>Nombre</th>
                                <th>Unidad Administrativa</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tableDependencias">
                            @forelse($dependencias as $item)
                            <tr data-search="{{ strtolower($item->id . ' ' . $item->clave_dependencia . ' ' . $item->nombre . ' ' . $item->unidad_administrativa) }}">
                                <td>{{ $item->id }}</td>
                                <td>{{ $item->clave_dependencia }}</td>
                                <td class="wrap-text">{{ $item->nombre }}</td>
                                <td>{{ $item->unidad_administrativa }}</td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn btn-warning btn-sm" onclick='editDependencia(@json($item))'>Editar</button>
                                        <button class="btn btn-danger btn-sm" onclick="deleteDependencia({{ $item->id }})">Eliminar</button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="5">No hay dependencias para mostrar.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div id="noResultsDep" class="no-results" style="display: none;">
                        <p>❌ No se encontraron resultados para tu búsqueda</p>
                    </div>
                </div>
            </section>
            
            <!-- TRÁMITES -->
            <section id="tramites-section" class="content-card data-section">
                <div class="title-content">
                    <h3>Trámites</h3>
                    <button class="btn btn-primary" onclick="openCreateModal('tramite')">+ Nuevo Trámite</button>
                </div>
                <hr>
                
                <!-- Búsqueda y Filtros -->
                <div class="search-filter-container">
                    <div class="search-box">
                        <input type="text" id="searchTramites" placeholder="🔍 Buscar en trámites (clave, descripción, cuota...)">
                        <button class="clear-search" id="clearSearchTra" onclick="clearSearch('tramites')">&times;</button>
                        <span class="search-icon">🔍</span>
                    </div>
                    <div class="filter-group">
                        <select class="filter-select" id="filterIVA" onchange="filterTramites()">
                            <option value="">Todos (IVA)</option>
                            <option value="si">Con IVA</option>
                            <option value="no">Sin IVA</option>
                        </select>
                        <select class="filter-select" id="filterObligatorio" onchange="filterTramites()">
                            <option value="">Todos (Obligatorio)</option>
                            <option value="S">Sí</option>
                            <option value="N">No</option>
                        </select>
                    </div>
                    <span class="results-count" id="resultsTra">Mostrando {{ $tramites->count() }} de {{ $tramites->count() }}</span>
                </div>
                
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Clave Dep. Siglas</th>
                                <th>Clave Trámite</th>
                                <th>Variante</th>
                                <th>Descripción</th>
                                <th>Uso Reservado</th>
                                <th>Fundamento Legal</th>
                                <th>Vigencia De</th>
                                <th>Vigencia Hasta</th>
                                <th>Vigencia Línea</th>
                                <th>Tipo Vigencia</th>
                                <th>Clave Contable</th>
                                <th>Obligatorio</th>
                                <th>Agrupador</th>
                                <th>Tipo Agrupador</th>
                                <th>Clave Periodicidad</th>
                                <th>Clave Periodo</th>
                                <th>Nombre Monto</th>
                                <th>Variable</th>
                                <th>Cuota</th>
                                <th>IVA</th>
                                <th>Monto IVA</th>
                                <th>Actualización</th>
                                <th>Recargos</th>
                                <th>Multa Corrección</th>
                                <th>Compensación</th>
                                <th>Saldo a Favor</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tableTramites">
                            @forelse($tramites as $item)
                            <tr data-search="{{ strtolower($item->id . ' ' . $item->clave_dependencia_siglas . ' ' . $item->clave_tramite . ' ' . $item->variante . ' ' . $item->descripcion . ' ' . $item->fundamento_legal . ' ' . $item->cuota . ' ' . $item->clave_contable) }}"
                                data-iva="{{ $item->iva ? 'si' : 'no' }}"
                                data-obligatorio="{{ $item->obligatorio ?? '' }}">
                                <td>{{ $item->id }}</td>
                                <td>{{ $item->clave_dependencia_siglas }}</td>
                                <td>{{ $item->clave_tramite }}</td>
                                <td>{{ $item->variante }}</td>
                                <td class="wrap-text">{{ $item->descripcion }}</td>
                                <td>{{ $item->tramite_usoreservado ?? 'N/A' }}</td>
                                <td class="wrap-text">{{ $item->fundamento_legal ?? 'N/A' }}</td>
                                <td>{{ $item->vigencia_tramite_de ? \Carbon\Carbon::parse($item->vigencia_tramite_de)->format('d/m/Y') : 'N/A' }}</td>
                                <td>{{ $item->vigencia_tramite_al ? \Carbon\Carbon::parse($item->vigencia_tramite_al)->format('d/m/Y') : 'N/A' }}</td>
                                <td>{{ $item->vigencia_lineacaptura ?? 'N/A' }}</td>
                                <td>{{ $item->tipo_vigencia ?? 'N/A' }}</td>
                                <td>{{ $item->clave_contable ?? 'N/A' }}</td>
                                <td>{{ $item->obligatorio ?? 'N/A' }}</td>
                                <td>{{ $item->agrupador ?? 'N/A' }}</td>
                                <td>{{ $item->tipo_agrupador ?? 'N/A' }}</td>
                                <td>{{ $item->clave_periodicidad }}</td>
                                <td>{{ $item->clave_periodo }}</td>
                                <td>{{ $item->nombre_monto ?? 'N/A' }}</td>
                                <td>{{ $item->variable ?? 'N/A' }}</td>
                                <td>${{ number_format($item->cuota, 2) }}</td>
                                <td>{{ $item->iva ? 'Sí' : 'No' }}</td>
                                <td>${{ number_format($item->monto_iva, 2) }}</td>
                                <td>{{ $item->actualizacion ?? 'N/A' }}</td>
                                <td>{{ $item->recargos ?? 'N/A' }}</td>
                                <td>{{ $item->multa_correccionfiscal ?? 'N/A' }}</td>
                                <td>{{ $item->compensacion ?? 'N/A' }}</td>
                                <td>{{ $item->saldo_favor ?? 'N/A' }}</td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn btn-warning btn-sm" onclick='editTramite(@json($item))'>Editar</button>
                                        <button class="btn btn-danger btn-sm" onclick="deleteTramite({{ $item->id }})">Eliminar</button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="28">No hay trámites para mostrar.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div id="noResultsTra" class="no-results" style="display: none;">
                        <p>❌ No se encontraron resultados para tu búsqueda o filtros</p>
                    </div>
                </div>
            </section>

            <!-- LÍNEAS DE CAPTURA -->
            <section id="lineas-captura-section" class="content-card data-section">
                <h3 class="title-content">Líneas de Captura Generadas</h3>
                <hr>
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Tipo Persona</th>
                                <th>CURP</th>
                                <th>RFC</th>
                                <th>Razón Social</th>
                                <th>Nombres</th>
                                <th>Apellido Paterno</th>
                                <th>Apellido Materno</th>
                                <th>Dependencia ID</th>
                                <th>Trámite ID</th>
                                <th>Solicitud #</th>
                                <th>Importe Cuota</th>
                                <th>Importe IVA</th>
                                <th>Importe Total</th>
                                <th>Estado Pago</th>
                                <th>Fecha Solicitud</th>
                                <th>Fecha Vigencia</th>
                                <th>Creado</th>
                                <th>Actualizado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($lineasCapturadas as $item)
                            <tr>
                                <td>{{ $item->id }}</td>
                                <td>{{ $item->tipo_persona }}</td>
                                <td>{{ $item->curp ?? 'N/A' }}</td>
                                <td>{{ $item->rfc }}</td>
                                <td class="wrap-text">{{ $item->razon_social ?? 'N/A' }}</td>
                                <td>{{ $item->nombres ?? 'N/A' }}</td>
                                <td>{{ $item->apellido_paterno ?? 'N/A' }}</td>
                                <td>{{ $item->apellido_materno ?? 'N/A' }}</td>
                                <td>{{ $item->dependencia_id }}</td>
                                <td>{{ $item->tramite_id ?? 'N/A' }}</td>
                                <td>{{ $item->solicitud ?? 'N/A' }}</td>
                                <td>${{ number_format($item->importe_cuota ?? 0, 2) }}</td>
                                <td>${{ number_format($item->importe_iva ?? 0, 2) }}</td>
                                <td>${{ number_format($item->importe_total ?? 0, 2) }}</td>
                                <td>{{ $item->estado_pago ?? 'pendiente' }}</td>
                                <td>{{ $item->fecha_solicitud ? \Carbon\Carbon::parse($item->fecha_solicitud)->format('d/m/Y H:i:s') : 'N/A' }}</td>
                                <td>{{ $item->fecha_vigencia ? \Carbon\Carbon::parse($item->fecha_vigencia)->format('d/m/Y') : 'N/A' }}</td>
                                <td>{{ $item->created_at ? $item->created_at->format('d/m/Y H:i:s') : 'N/A' }}</td>
                                <td>{{ $item->updated_at ? $item->updated_at->format('d/m/Y H:i:s') : 'N/A' }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="19">No se han generado líneas de captura.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- USUARIOS -->
            <section id="users-section" class="content-card data-section">
                <h3 class="title-content">Usuarios del Sistema</h3>
                <hr>
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Email</th>
                                <th>Fecha de Registro</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                            <tr>
                                <td>{{ $user->id }}</td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->created_at ? $user->created_at->format('d/m/Y H:i') : 'N/A' }}</td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn btn-warning btn-sm" onclick='editUser(@json($user))'>Editar</button>
                                        <button class="btn btn-danger btn-sm" onclick="deleteUser({{ $user->id }})">Eliminar</button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="5">No hay usuarios para mostrar.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </main>
    </div>

    <!-- MODAL DEPENDENCIAS -->
    <div id="modalDependencia" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modalDependenciaTitle">Nueva Dependencia</h3>
                <span class="close" onclick="closeModal('modalDependencia')">&times;</span>
            </div>
            <form id="formDependencia" method="POST">
                @csrf
                <input type="hidden" name="_method" id="dependenciaMethod" value="POST">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="dep_nombre">Nombre *</label>
                        <input type="text" class="form-control" id="dep_nombre" name="nombre" required>
                    </div>
                    <div class="form-group">
                        <label for="dep_clave">Clave Dependencia *</label>
                        <input type="text" class="form-control" id="dep_clave" name="clave_dependencia" maxlength="3" required>
                    </div>
                    <div class="form-group">
                        <label for="dep_unidad">Unidad Administrativa *</label>
                        <input type="text" class="form-control" id="dep_unidad" name="unidad_administrativa" maxlength="3" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" onclick="closeModal('modalDependencia')">Cancelar</button>
                    <button type="submit" class="btn btn-success">Guardar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL TRÁMITES -->
    <div id="modalTramite" class="modal">
        <div class="modal-content" style="max-width: 800px;">
            <div class="modal-header">
                <h3 id="modalTramiteTitle">Nuevo Trámite</h3>
                <span class="close" onclick="closeModal('modalTramite')">&times;</span>
            </div>
            <form id="formTramite" method="POST">
                @csrf
                <input type="hidden" name="_method" id="tramiteMethod" value="POST">
                <div class="modal-body">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                        <div class="form-group">
                            <label for="tra_clave_dep">Clave Dependencia Siglas *</label>
                            <input type="text" class="form-control" id="tra_clave_dep" name="clave_dependencia_siglas" maxlength="10" required>
                        </div>
                        <div class="form-group">
                            <label for="tra_clave">Clave Trámite *</label>
                            <input type="text" class="form-control" id="tra_clave" name="clave_tramite" maxlength="30" required>
                        </div>
                        <div class="form-group">
                            <label for="tra_variante">Variante *</label>
                            <input type="text" class="form-control" id="tra_variante" name="variante" maxlength="2" required>
                        </div>
                        <div class="form-group">
                            <label for="tra_uso_reservado">Uso Reservado</label>
                            <input type="text" class="form-control" id="tra_uso_reservado" name="tramite_usoreservado" maxlength="1">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="tra_descripcion">Descripción *</label>
                        <textarea class="form-control" id="tra_descripcion" name="descripcion" rows="2" maxlength="200" required></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="tra_fundamento">Fundamento Legal</label>
                        <textarea class="form-control" id="tra_fundamento" name="fundamento_legal" rows="2" maxlength="200"></textarea>
                    </div>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px;">
                        <div class="form-group">
                            <label for="tra_vigencia_de">Vigencia De</label>
                            <input type="date" class="form-control" id="tra_vigencia_de" name="vigencia_tramite_de">
                        </div>
                        <div class="form-group">
                            <label for="tra_vigencia">Vigencia Hasta</label>
                            <input type="date" class="form-control" id="tra_vigencia" name="vigencia_tramite_al">
                        </div>
                        <div class="form-group">
                            <label for="tra_vigencia_linea">Vigencia Línea</label>
                            <input type="number" class="form-control" id="tra_vigencia_linea" name="vigencia_lineacaptura">
                        </div>
                    </div>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px;">
                        <div class="form-group">
                            <label for="tra_tipo_vigencia">Tipo Vigencia</label>
                            <input type="text" class="form-control" id="tra_tipo_vigencia" name="tipo_vigencia" maxlength="1">
                        </div>
                        <div class="form-group">
                            <label for="tra_clave_contable">Clave Contable</label>
                            <input type="number" class="form-control" id="tra_clave_contable" name="clave_contable">
                        </div>
                        <div class="form-group">
                            <label for="tra_obligatorio">Obligatorio</label>
                            <input type="text" class="form-control" id="tra_obligatorio" name="obligatorio" maxlength="1">
                        </div>
                    </div>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px;">
                        <div class="form-group">
                            <label for="tra_agrupador">Agrupador</label>
                            <input type="text" class="form-control" id="tra_agrupador" name="agrupador" maxlength="1">
                        </div>
                        <div class="form-group">
                            <label for="tra_tipo_agrupador">Tipo Agrupador</label>
                            <input type="text" class="form-control" id="tra_tipo_agrupador" name="tipo_agrupador" maxlength="1">
                        </div>
                        <div class="form-group">
                            <label for="tra_variable">Variable</label>
                            <input type="text" class="form-control" id="tra_variable" name="variable" maxlength="1">
                        </div>
                    </div>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                        <div class="form-group">
                            <label for="tra_clave_periodicidad">Clave Periodicidad *</label>
                            <input type="text" class="form-control" id="tra_clave_periodicidad" name="clave_periodicidad" maxlength="1" value="N" required>
                        </div>
                        <div class="form-group">
                            <label for="tra_clave_periodo">Clave Periodo *</label>
                            <input type="text" class="form-control" id="tra_clave_periodo" name="clave_periodo" maxlength="3" value="099" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="tra_nombre_monto">Nombre Monto</label>
                        <input type="text" class="form-control" id="tra_nombre_monto" name="nombre_monto" maxlength="100">
                    </div>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                        <div class="form-group">
                            <label for="tra_cuota">Cuota *</label>
                            <input type="number" class="form-control" id="tra_cuota" name="cuota" step="0.01" min="0" required>
                        </div>
                        <div class="form-group">
                            <div class="form-check" style="margin-top: 30px;">
                                <input type="checkbox" id="tra_iva" name="iva" value="1">
                                <label for="tra_iva">Aplica IVA</label>
                            </div>
                        </div>
                    </div>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr 1fr 1fr; gap: 15px;">
                        <div class="form-group">
                            <label for="tra_actualizacion">Actualización</label>
                            <input type="text" class="form-control" id="tra_actualizacion" name="actualizacion" maxlength="1">
                        </div>
                        <div class="form-group">
                            <label for="tra_recargos">Recargos</label>
                            <input type="text" class="form-control" id="tra_recargos" name="recargos" maxlength="1">
                        </div>
                        <div class="form-group">
                            <label for="tra_multa">Multa Corrección</label>
                            <input type="text" class="form-control" id="tra_multa" name="multa_correccionfiscal" maxlength="1">
                        </div>
                        <div class="form-group">
                            <label for="tra_compensacion">Compensación</label>
                            <input type="text" class="form-control" id="tra_compensacion" name="compensacion" maxlength="1">
                        </div>
                        <div class="form-group">
                            <label for="tra_saldo_favor">Saldo a Favor</label>
                            <input type="text" class="form-control" id="tra_saldo_favor" name="saldo_favor" maxlength="1">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" onclick="closeModal('modalTramite')">Cancelar</button>
                    <button type="submit" class="btn btn-success">Guardar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL LÍNEAS DE CAPTURA -->
    <div id="modalLineaCaptura" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Editar Línea de Captura</h3>
                <span class="close" onclick="closeModal('modalLineaCaptura')">&times;</span>
            </div>
            <form id="formLineaCaptura" method="POST">
                @csrf
                <input type="hidden" name="_method" value="PUT">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="lin_estado">Estado de Pago *</label>
                        <select class="form-control" id="lin_estado" name="estado_pago" required>
                            <option value="pendiente">Pendiente</option>
                            <option value="pagado">Pagado</option>
                            <option value="cancelado">Cancelado</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="lin_vigencia">Fecha de Vigencia</label>
                        <input type="date" class="form-control" id="lin_vigencia" name="fecha_vigencia">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" onclick="closeModal('modalLineaCaptura')">Cancelar</button>
                    <button type="submit" class="btn btn-success">Guardar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL USUARIOS -->
    <div id="modalUser" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Editar Usuario</h3>
                <span class="close" onclick="closeModal('modalUser')">&times;</span>
            </div>
            <form id="formUser" method="POST">
                @csrf
                <input type="hidden" name="_method" value="PUT">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="user_name">Nombre *</label>
                        <input type="text" class="form-control" id="user_name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="user_email">Email *</label>
                        <input type="email" class="form-control" id="user_email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="user_password">Nueva Contraseña (dejar en blanco para no cambiar)</label>
                        <input type="password" class="form-control" id="user_password" name="password" minlength="8">
                    </div>
                    <div class="form-group">
                        <label for="user_password_confirmation">Confirmar Contraseña</label>
                        <input type="password" class="form-control" id="user_password_confirmation" name="password_confirmation" minlength="8">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" onclick="closeModal('modalUser')">Cancelar</button>
                    <button type="submit" class="btn btn-success">Guardar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL CONFIRMACIÓN ELIMINAR -->
    <div id="modalDelete" class="modal">
        <div class="modal-content" style="max-width: 400px;">
            <div class="modal-header">
                <h3>Confirmar Eliminación</h3>
                <span class="close" onclick="closeModal('modalDelete')">&times;</span>
            </div>
            <form id="formDelete" method="POST">
                @csrf
                <input type="hidden" name="_method" value="DELETE">
                <div class="modal-body">
                    <p>¿Está seguro que desea eliminar este registro? Esta acción no se puede deshacer.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" onclick="closeModal('modalDelete')">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Eliminar</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Navegación entre secciones
        document.addEventListener('DOMContentLoaded', function() {
            const navLinks = document.querySelectorAll('.sidebar .nav-link');
            const sections = document.querySelectorAll('.content-card');

            function showSection(targetId) {
                sections.forEach(section => {
                    section.style.display = (section.id === targetId) ? 'block' : 'none';
                });
            }

            showSection('inicio-section');

            navLinks.forEach(link => {
                link.addEventListener('click', function(event) {
                    event.preventDefault();
                    navLinks.forEach(nav => nav.classList.remove('active'));
                    this.classList.add('active');
                    const targetId = this.getAttribute('data-target');
                    showSection(targetId);
                });
            });

            // Auto-cerrar alertas después de 5 segundos
            setTimeout(() => {
                const alerts = document.querySelectorAll('.alert');
                alerts.forEach(alert => alert.style.display = 'none');
            }, 5000);

            // Inicializar búsqueda
            initializeSearch();
        });

        // ========== SISTEMA DE BÚSQUEDA ==========
        function initializeSearch() {
            // Búsqueda en Dependencias
            const searchDep = document.getElementById('searchDependencias');
            if (searchDep) {
                searchDep.addEventListener('input', function() {
                    filterTable('dependencias', this.value);
                    toggleClearButton('clearSearchDep', this.value);
                });
            }

            // Búsqueda en Trámites
            const searchTra = document.getElementById('searchTramites');
            if (searchTra) {
                searchTra.addEventListener('input', function() {
                    filterTramites();
                    toggleClearButton('clearSearchTra', this.value);
                });
            }
        }

        function toggleClearButton(buttonId, value) {
            const button = document.getElementById(buttonId);
            if (button) {
                button.style.display = value ? 'block' : 'none';
            }
        }

        function clearSearch(type) {
            if (type === 'dependencias') {
                document.getElementById('searchDependencias').value = '';
                filterTable('dependencias', '');
                document.getElementById('clearSearchDep').style.display = 'none';
            } else if (type === 'tramites') {
                document.getElementById('searchTramites').value = '';
                document.getElementById('filterIVA').value = '';
                document.getElementById('filterObligatorio').value = '';
                filterTramites();
                document.getElementById('clearSearchTra').style.display = 'none';
            }
        }

        function filterTable(type, searchTerm) {
            const tableId = type === 'dependencias' ? 'tableDependencias' : 'tableTramites';
            const resultsId = type === 'dependencias' ? 'resultsDep' : 'resultsTra';
            const noResultsId = type === 'dependencias' ? 'noResultsDep' : 'noResultsTra';
            
            const table = document.getElementById(tableId);
            const rows = table.getElementsByTagName('tr');
            const searchLower = searchTerm.toLowerCase().trim();
            
            let visibleCount = 0;
            const totalCount = rows.length;

            for (let i = 0; i < rows.length; i++) {
                const searchData = rows[i].getAttribute('data-search');
                
                if (searchData) {
                    if (searchLower === '' || searchData.includes(searchLower)) {
                        rows[i].style.display = '';
                        visibleCount++;
                    } else {
                        rows[i].style.display = 'none';
                    }
                }
            }

            // Actualizar contador
            document.getElementById(resultsId).textContent = `Mostrando ${visibleCount} de ${totalCount}`;
            
            // Mostrar mensaje si no hay resultados
            const noResults = document.getElementById(noResultsId);
            if (visibleCount === 0) {
                table.parentElement.style.display = 'none';
                noResults.style.display = 'block';
            } else {
                table.parentElement.style.display = 'block';
                noResults.style.display = 'none';
            }
        }

        function filterTramites() {
            const searchTerm = document.getElementById('searchTramites').value.toLowerCase().trim();
            const filterIVA = document.getElementById('filterIVA').value;
            const filterObligatorio = document.getElementById('filterObligatorio').value;
            
            const table = document.getElementById('tableTramites');
            const rows = table.getElementsByTagName('tr');
            
            let visibleCount = 0;
            const totalCount = rows.length;

            for (let i = 0; i < rows.length; i++) {
                const row = rows[i];
                const searchData = row.getAttribute('data-search');
                
                if (searchData) {
                    let showRow = true;
                    
                    // Filtro de búsqueda
                    if (searchTerm !== '' && !searchData.includes(searchTerm)) {
                        showRow = false;
                    }
                    
                    // Filtro de IVA
                    if (filterIVA !== '') {
                        const rowIVA = row.getAttribute('data-iva');
                        if (rowIVA !== filterIVA) {
                            showRow = false;
                        }
                    }
                    
                    // Filtro de Obligatorio
                    if (filterObligatorio !== '') {
                        const rowObligatorio = row.getAttribute('data-obligatorio');
                        if (rowObligatorio !== filterObligatorio) {
                            showRow = false;
                        }
                    }
                    
                    row.style.display = showRow ? '' : 'none';
                    if (showRow) visibleCount++;
                }
            }

            // Actualizar contador
            document.getElementById('resultsTra').textContent = `Mostrando ${visibleCount} de ${totalCount}`;
            
            // Mostrar mensaje si no hay resultados
            const noResults = document.getElementById('noResultsTra');
            if (visibleCount === 0) {
                table.parentElement.style.display = 'none';
                noResults.style.display = 'block';
            } else {
                table.parentElement.style.display = 'block';
                noResults.style.display = 'none';
            }
        }

        // Funciones para manejo de modales
        function openModal(modalId) {
            document.getElementById(modalId).classList.add('show');
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.remove('show');
        }

        // Cerrar modal al hacer clic fuera
        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                event.target.classList.remove('show');
            }
        }

        // ========== DEPENDENCIAS ==========
        function openCreateModal(type) {
            if (type === 'dependencia') {
                document.getElementById('modalDependenciaTitle').textContent = 'Nueva Dependencia';
                document.getElementById('formDependencia').action = '{{ route("admin.dependencias.store") }}';
                document.getElementById('dependenciaMethod').value = 'POST';
                document.getElementById('formDependencia').reset();
                openModal('modalDependencia');
            } else if (type === 'tramite') {
                document.getElementById('modalTramiteTitle').textContent = 'Nuevo Trámite';
                document.getElementById('formTramite').action = '{{ route("admin.tramites.store") }}';
                document.getElementById('tramiteMethod').value = 'POST';
                document.getElementById('formTramite').reset();
                openModal('modalTramite');
            }
        }

        function editDependencia(item) {
            document.getElementById('modalDependenciaTitle').textContent = 'Editar Dependencia';
            document.getElementById('formDependencia').action = `/admin/dependencias/${item.id}`;
            document.getElementById('dependenciaMethod').value = 'PUT';
            document.getElementById('dep_nombre').value = item.nombre;
            document.getElementById('dep_clave').value = item.clave_dependencia;
            document.getElementById('dep_unidad').value = item.unidad_administrativa;
            openModal('modalDependencia');
        }

        function deleteDependencia(id) {
            document.getElementById('formDelete').action = `/admin/dependencias/${id}`;
            openModal('modalDelete');
        }

        // ========== TRÁMITES ==========
        function editTramite(item) {
            document.getElementById('modalTramiteTitle').textContent = 'Editar Trámite';
            document.getElementById('formTramite').action = `/admin/tramites/${item.id}`;
            document.getElementById('tramiteMethod').value = 'PUT';
            document.getElementById('tra_clave_dep').value = item.clave_dependencia_siglas;
            document.getElementById('tra_clave').value = item.clave_tramite;
            document.getElementById('tra_variante').value = item.variante;
            document.getElementById('tra_descripcion').value = item.descripcion;
            document.getElementById('tra_uso_reservado').value = item.tramite_usoreservado || '';
            document.getElementById('tra_fundamento').value = item.fundamento_legal || '';
            document.getElementById('tra_vigencia_de').value = item.vigencia_tramite_de || '';
            document.getElementById('tra_vigencia').value = item.vigencia_tramite_al || '';
            document.getElementById('tra_vigencia_linea').value = item.vigencia_lineacaptura || '';
            document.getElementById('tra_tipo_vigencia').value = item.tipo_vigencia || '';
            document.getElementById('tra_clave_contable').value = item.clave_contable || '';
            document.getElementById('tra_obligatorio').value = item.obligatorio || '';
            document.getElementById('tra_agrupador').value = item.agrupador || '';
            document.getElementById('tra_tipo_agrupador').value = item.tipo_agrupador || '';
            document.getElementById('tra_clave_periodicidad').value = item.clave_periodicidad || 'N';
            document.getElementById('tra_clave_periodo').value = item.clave_periodo || '099';
            document.getElementById('tra_nombre_monto').value = item.nombre_monto || '';
            document.getElementById('tra_variable').value = item.variable || '';
            document.getElementById('tra_cuota').value = item.cuota;
            document.getElementById('tra_iva').checked = item.iva == 1;
            document.getElementById('tra_actualizacion').value = item.actualizacion || '';
            document.getElementById('tra_recargos').value = item.recargos || '';
            document.getElementById('tra_multa').value = item.multa_correccionfiscal || '';
            document.getElementById('tra_compensacion').value = item.compensacion || '';
            document.getElementById('tra_saldo_favor').value = item.saldo_favor || '';
            openModal('modalTramite');
        }

        function deleteTramite(id) {
            document.getElementById('formDelete').action = `/admin/tramites/${id}`;
            openModal('modalDelete');
        }

        // ========== LÍNEAS DE CAPTURA ==========
        function editLineaCaptura(item) {
            document.getElementById('formLineaCaptura').action = `/admin/lineas-captura/${item.id}`;
            document.getElementById('lin_estado').value = item.estado_pago;
            document.getElementById('lin_vigencia').value = item.fecha_vigencia || '';
            openModal('modalLineaCaptura');
        }

        function deleteLineaCaptura(id) {
            document.getElementById('formDelete').action = `/admin/lineas-captura/${id}`;
            openModal('modalDelete');
        }

        // ========== USUARIOS ==========
        function editUser(user) {
            document.getElementById('formUser').action = `/admin/usuarios/${user.id}`;
            document.getElementById('user_name').value = user.name;
            document.getElementById('user_email').value = user.email;
            document.getElementById('user_password').value = '';
            document.getElementById('user_password_confirmation').value = '';
            openModal('modalUser');
        }

        function deleteUser(id) {
            document.getElementById('formDelete').action = `/admin/usuarios/${id}`;
            openModal('modalDelete');
        }
    </script>
</body>
</html>