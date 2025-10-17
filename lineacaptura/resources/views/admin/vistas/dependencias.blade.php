<section id="dependencias-section" class="content-card data-section">
  <div class="title-content">
    <h3>Dependencias</h3>
    <button class="btn btn-primary" onclick="openCreateModalDependencia()">+ Nueva dependencia</button>
  </div>
  <hr>

  <div class="search-filter-container">
    <div class="search-box">
      <img src="https://img.icons8.com/ios-filled/50/858796/search.png" class="search-icon" alt="buscar">
      <input type="text" id="searchDependencias" placeholder="Buscar dependencias (clave, nombre, unidad)">
      <button class="clear-search" id="clearSearchDep" onclick="clearSearchDependencias()">&times;</button>
    </div>
    <span class="results-count" id="resultsDep">Mostrando {{ $dependencias->count() }} de {{ $dependencias->count() }}</span>
  </div>

  <div class="table-container">
    <table class="data-table">
      <thead>
        <tr><th>ID</th><th>Clave</th><th>Nombre</th><th>Unidad Administrativa</th><th>Acciones</th></tr>
      </thead>
      <tbody id="tableDependencias">
        @forelse($dependencias as $item)
          <tr data-search="{{ strtolower($item->id.' '.$item->clave_dependencia.' '.$item->nombre.' '.$item->unidad_administrativa) }}">
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
    <div id="noResultsDep" class="no-results" style="display:none;">❌ No se encontraron resultados</div>
  </div>

  {{-- MODAL Dependencia --}}
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
          <div class="form-group"><label>Nombre *</label><input class="form-control" id="dep_nombre" name="nombre" required></div>
          <div class="form-group"><label>Clave Dependencia *</label><input class="form-control" id="dep_clave" name="clave_dependencia" maxlength="3" required></div>
          <div class="form-group"><label>Unidad Administrativa *</label><input class="form-control" id="dep_unidad" name="unidad_administrativa" maxlength="3" required></div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger" onclick="closeModal('modalDependencia')">Cancelar</button>
          <button type="submit" class="btn btn-success">Guardar</button>
        </div>
      </form>
    </div>
  </div>

  {{-- JS de esta sección --}}
  <script>
    // Búsqueda
    const depInput = document.getElementById('searchDependencias');
    if(depInput){
      depInput.addEventListener('input', e => {
        filterDep(e.target.value);
        toggleClearButton('clearSearchDep', e.target.value);
      });
    }
    function clearSearchDependencias(){ depInput.value=''; filterDep(''); toggleClearButton('clearSearchDep',''); }
    function filterDep(q){
      const rows = document.querySelectorAll('#tableDependencias tr[data-search]');
      const total = rows.length; let visible=0;
      q = (q||'').toLowerCase().trim();
      rows.forEach(r => {
        const show = !q || r.getAttribute('data-search').includes(q);
        r.style.display = show ? '' : 'none';
        if(show) visible++;
      });
      document.getElementById('resultsDep').textContent = `Mostrando ${visible} de ${total}`;
      document.getElementById('noResultsDep').style.display = visible? 'none':'block';
      document.getElementById('tableDependencias').parentElement.style.display = visible? 'block':'none';
    }

    // CRUD
    function openCreateModalDependencia(){
      document.getElementById('modalDependenciaTitle').textContent='Nueva Dependencia';
      document.getElementById('formDependencia').action='{{ route("admin.dependencias.store") }}';
      document.getElementById('dependenciaMethod').value='POST';
      document.getElementById('formDependencia').reset();
      openModal('modalDependencia');
    }
    function editDependencia(item){
      document.getElementById('modalDependenciaTitle').textContent='Editar Dependencia';
      document.getElementById('formDependencia').action=`/admin/dependencias/${item.id}`;
      document.getElementById('dependenciaMethod').value='PUT';
      dep_nombre.value=item.nombre; dep_clave.value=item.clave_dependencia; dep_unidad.value=item.unidad_administrativa;
      openModal('modalDependencia');
    }
    function deleteDependencia(id){
      const f = document.getElementById('formDelete') || (()=>{
        const m = document.createElement('div'); m.id='modalDelete'; m.className='modal';
        m.innerHTML = `<div class="modal-content" style="max-width:400px;"><div class="modal-header"><h3>Confirmar Eliminación</h3><span class="close" onclick="closeModal('modalDelete')">&times;</span></div><form id="formDelete" method="POST">@csrf<input type="hidden" name="_method" value="DELETE"><div class="modal-body"><p>¿Está seguro que desea eliminar este registro?</p></div><div class="modal-footer"><button type="button" class="btn btn-primary" onclick="closeModal('modalDelete')">Cancelar</button><button type="submit" class="btn btn-danger">Eliminar</button></div></form></div>`;
        document.body.appendChild(m); return document.getElementById('formDelete');
      })();
      f.action = `/admin/dependencias/${id}`; openModal('modalDelete');
    }
  </script>
</section>
