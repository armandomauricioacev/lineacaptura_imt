<section id="lineas-captura-section" class="content-card data-section">
  <h3 class="title-content">Líneas de captura generadas</h3>
  <hr>

  <div class="search-filter-container">
    <div class="search-box">
      <img src="https://img.icons8.com/ios-filled/50/858796/search.png" class="search-icon" alt="buscar">
      <input type="text" id="searchLineas" placeholder="Buscar (CURP, RFC, nombre, estado...)">
      <button class="clear-search" id="clearSearchLineas" onclick="clearSearchLineas()">&times;</button>
    </div>
    <span class="results-count" id="resultsLineas">Mostrando {{ $lineasCapturadas->count() }} de {{ $lineasCapturadas->count() }}</span>
  </div>

  <div class="table-container">
    <table class="data-table">
      <thead>
        <tr>
          <th>ID</th><th>Tipo Persona</th><th>CURP</th><th>RFC</th><th>Razón Social</th><th>Nombres</th>
          <th>Apellido Paterno</th><th>Apellido Materno</th><th>Dependencia ID</th><th>Trámite ID</th>
          <th>Solicitud #</th><th>Importe Cuota</th><th>Importe IVA</th><th>Importe Total</th>
          <th>Estado Pago</th><th>Fecha Solicitud</th><th>Fecha Vigencia</th><th>Creado</th><th>Actualizado</th>
        </tr>
      </thead>
      <tbody id="tableLineas">
        @forelse($lineasCapturadas as $item)
          <tr data-search="{{ strtolower($item->id.' '.$item->tipo_persona.' '.($item->curp ?? '').' '.$item->rfc.' '.($item->razon_social ?? '').' '.($item->nombres ?? '').' '.($item->apellido_paterno ?? '').' '.($item->apellido_materno ?? '').' '.$item->dependencia_id.' '.($item->tramite_id ?? '').' '.($item->solicitud ?? '').' '.$item->importe_cuota.' '.$item->importe_iva.' '.$item->importe_total.' '.($item->estado_pago ?? 'pendiente')) }}">
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
    <div id="noResultsLineas" class="no-results" style="display:none;">❌ No se encontraron resultados</div>
  </div>

  {{-- MODAL Línea de captura --}}
  <div id="modalLineaCaptura" class="modal">
    <div class="modal-content">
      <div class="modal-header">
        <h3>Editar Línea de Captura</h3>
        <span class="close" onclick="closeModal('modalLineaCaptura')">&times;</span>
      </div>
      <form id="formLineaCaptura" method="POST">
        @csrf <input type="hidden" name="_method" value="PUT">
        <div class="modal-body">
          <div class="form-group"><label>Estado de Pago *</label>
            <select class="form-control" id="lin_estado" name="estado_pago" required>
              <option value="pendiente">Pendiente</option><option value="pagado">Pagado</option><option value="cancelado">Cancelado</option>
            </select>
          </div>
          <div class="form-group"><label>Fecha de Vigencia</label><input type="date" class="form-control" id="lin_vigencia" name="fecha_vigencia"></div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger" onclick="closeModal('modalLineaCaptura')">Cancelar</button>
          <button type="submit" class="btn btn-success">Guardar</button>
        </div>
      </form>
    </div>
  </div>

  {{-- JS de esta sección --}}
  <script>
    const linInput = document.getElementById('searchLineas');
    if(linInput){ linInput.addEventListener('input', e => { filterLineas(e.target.value); toggleClearButton('clearSearchLineas', e.target.value); }); }
    function clearSearchLineas(){ linInput.value=''; filterLineas(''); toggleClearButton('clearSearchLineas',''); }
    function filterLineas(q){
      const rows = document.querySelectorAll('#tableLineas tr[data-search]'); const total=rows.length; let visible=0;
      q=(q||'').toLowerCase().trim();
      rows.forEach(r => { const show = !q || r.getAttribute('data-search').includes(q); r.style.display = show?'':'none'; if(show) visible++; });
      document.getElementById('resultsLineas').textContent = `Mostrando ${visible} de ${total}`;
      document.getElementById('noResultsLineas').style.display = visible? 'none':'block';
      document.getElementById('tableLineas').parentElement.style.display = visible? 'block':'none';
    }
    function editLineaCaptura(item){
      document.getElementById('formLineaCaptura').action=`/admin/lineas-captura/${item.id}`;
      document.getElementById('lin_estado').value=item.estado_pago || 'pendiente';
      document.getElementById('lin_vigencia').value=item.fecha_vigencia || '';
      openModal('modalLineaCaptura');
    }
    function deleteLineaCaptura(id){
      ensureDeleteModal(); document.getElementById('formDelete').action=`/admin/lineas-captura/${id}`; openModal('modalDelete');
    }
    function ensureDeleteModal(){
      if(!document.getElementById('modalDelete')){
        const m = document.createElement('div'); m.id='modalDelete'; m.className='modal';
        m.innerHTML = `<div class="modal-content" style="max-width:400px;"><div class="modal-header"><h3>Confirmar Eliminación</h3><span class="close" onclick="closeModal('modalDelete')">&times;</span></div><form id="formDelete" method="POST">@csrf<input type="hidden" name="_method" value="DELETE"><div class="modal-body"><p>¿Está seguro que desea eliminar este registro?</p></div><div class="modal-footer"><button type="button" class="btn btn-primary" onclick="closeModal('modalDelete')">Cancelar</button><button type="submit" class="btn btn-danger">Eliminar</button></div></form></div>`;
        document.body.appendChild(m);
      }
    }
  </script>
</section>
