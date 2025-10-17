<section id="tramites-section" class="content-card data-section">
  <div class="title-content">
    <h3>Trámites</h3>
    <button class="btn btn-primary" onclick="openCreateModalTramite()">+ Nuevo trámite</button>
  </div>
  <hr>

  <div class="search-filter-container">
    <div class="search-box">
      <img src="https://img.icons8.com/ios-filled/50/858796/search.png" class="search-icon" alt="buscar">
      <input type="text" id="searchTramites" placeholder="Buscar en trámites (clave, descripción, cuota...)">
      <button class="clear-search" id="clearSearchTra" onclick="clearSearchTramites()">&times;</button>
    </div>
    <div class="filter-group">
      <select class="filter-select" id="filterIVA" onchange="filterTramites()">
        <option value="">Todos (IVA)</option><option value="si">Con IVA</option><option value="no">Sin IVA</option>
      </select>
    </div>
    <span class="results-count" id="resultsTra">Mostrando {{ $tramites->count() }} de {{ $tramites->count() }}</span>
  </div>

  <div class="table-container">
    <table class="data-table">
      <thead>
        <tr>
          <th>ID</th><th>Clave Dep. Siglas</th><th>Clave Trámite</th><th>Variante</th>
          <th>Descripción</th><th>Uso Reservado</th><th>Fundamento Legal</th>
          <th>Vigencia De</th><th>Vigencia Hasta</th><th>Vigencia Línea</th><th>Tipo Vigencia</th>
          <th>Clave Contable</th><th>Obligatorio</th><th>Agrupador</th><th>Tipo Agrupador</th>
          <th>Clave Periodicidad</th><th>Clave Periodo</th><th>Nombre Monto</th>
          <th>Variable</th><th>Cuota</th><th>IVA</th><th>Monto IVA</th>
          <th>Actualización</th><th>Recargos</th><th>Multa Corrección</th><th>Compensación</th><th>Saldo a Favor</th><th>Acciones</th>
        </tr>
      </thead>
      <tbody id="tableTramites">
        @forelse($tramites as $item)
          <tr data-search="{{ strtolower($item->id.' '.$item->clave_dependencia_siglas.' '.$item->clave_tramite.' '.$item->variante.' '.$item->descripcion.' '.$item->fundamento_legal.' '.$item->cuota.' '.$item->clave_contable.' '.$item->monto_iva.' '.($item->iva?'si con iva':'no sin iva')) }}" data-iva="{{ $item->iva ? 'si' : 'no' }}">
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
    <div id="noResultsTra" class="no-results" style="display:none;">❌ No se encontraron resultados</div>
  </div>

  {{-- MODAL Trámite --}}
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
          <div class="grid-2">
            <div class="form-group"><label>Clave Dep. Siglas *</label><input class="form-control" id="tra_clave_dep" name="clave_dependencia_siglas" maxlength="10" required></div>
            <div class="form-group"><label>Clave Trámite *</label><input class="form-control" id="tra_clave" name="clave_tramite" maxlength="30" required></div>
            <div class="form-group"><label>Variante *</label><input class="form-control" id="tra_variante" name="variante" maxlength="2" required></div>
            <div class="form-group"><label>Uso Reservado</label><input class="form-control" id="tra_uso_reservado" name="tramite_usoreservado" maxlength="1"></div>
          </div>
          <div class="form-group"><label>Descripción *</label><textarea class="form-control" id="tra_descripcion" name="descripcion" rows="2" maxlength="200" required></textarea></div>
          <div class="form-group"><label>Fundamento Legal</label><textarea class="form-control" id="tra_fundamento" name="fundamento_legal" rows="2" maxlength="200"></textarea></div>
          <div class="grid-3">
            <div class="form-group"><label>Vigencia De</label><input type="date" class="form-control" id="tra_vigencia_de" name="vigencia_tramite_de"></div>
            <div class="form-group"><label>Vigencia Hasta</label><input type="date" class="form-control" id="tra_vigencia" name="vigencia_tramite_al"></div>
            <div class="form-group"><label>Vigencia Línea</label><input type="number" class="form-control" id="tra_vigencia_linea" name="vigencia_lineacaptura"></div>
          </div>
          <div class="grid-3">
            <div class="form-group"><label>Tipo Vigencia</label><input class="form-control" id="tra_tipo_vigencia" name="tipo_vigencia" maxlength="1"></div>
            <div class="form-group"><label>Clave Contable</label><input type="number" class="form-control" id="tra_clave_contable" name="clave_contable"></div>
            <div class="form-group"><label>Obligatorio</label><input class="form-control" id="tra_obligatorio" name="obligatorio" maxlength="1"></div>
          </div>
          <div class="grid-3">
            <div class="form-group"><label>Agrupador</label><input class="form-control" id="tra_agrupador" name="agrupador" maxlength="1"></div>
            <div class="form-group"><label>Tipo Agrupador</label><input class="form-control" id="tra_tipo_agrupador" name="tipo_agrupador" maxlength="1"></div>
            <div class="form-group"><label>Variable</label><input class="form-control" id="tra_variable" name="variable" maxlength="1"></div>
          </div>
          <div class="grid-2">
            <div class="form-group"><label>Clave Periodicidad *</label><input class="form-control" id="tra_clave_periodicidad" name="clave_periodicidad" maxlength="1" value="N" required></div>
            <div class="form-group"><label>Clave Periodo *</label><input class="form-control" id="tra_clave_periodo" name="clave_periodo" maxlength="3" value="099" required></div>
          </div>
          <div class="form-group"><label>Nombre Monto</label><input class="form-control" id="tra_nombre_monto" name="nombre_monto" maxlength="100"></div>
          <div class="grid-2">
            <div class="form-group"><label>Cuota *</label><input type="number" class="form-control" id="tra_cuota" name="cuota" step="0.01" min="0" required></div>
            <div class="form-group"><div class="form-check" style="margin-top:30px;"><input type="checkbox" id="tra_iva" name="iva" value="1"><label for="tra_iva">Aplica IVA</label></div></div>
          </div>
          <div class="grid-5">
            <div class="form-group"><label>Actualización</label><input class="form-control" id="tra_actualizacion" name="actualizacion" maxlength="1"></div>
            <div class="form-group"><label>Recargos</label><input class="form-control" id="tra_recargos" name="recargos" maxlength="1"></div>
            <div class="form-group"><label>Multa Corrección</label><input class="form-control" id="tra_multa" name="multa_correccionfiscal" maxlength="1"></div>
            <div class="form-group"><label>Compensación</label><input class="form-control" id="tra_compensacion" name="compensacion" maxlength="1"></div>
            <div class="form-group"><label>Saldo a Favor</label><input class="form-control" id="tra_saldo_favor" name="saldo_favor" maxlength="1"></div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger" onclick="closeModal('modalTramite')">Cancelar</button>
          <button type="submit" class="btn btn-success">Guardar</button>
        </div>
      </form>
    </div>
  </div>

  {{-- JS de esta sección --}}
  <script>
    const traInput = document.getElementById('searchTramites');
    if(traInput){ traInput.addEventListener('input', e => { filterTramites(); toggleClearButton('clearSearchTra', e.target.value); }); }
    function clearSearchTramites(){ document.getElementById('searchTramites').value=''; document.getElementById('filterIVA').value=''; filterTramites(); toggleClearButton('clearSearchTra',''); }
    function filterTramites(){
      const q = (document.getElementById('searchTramites').value||'').toLowerCase().trim();
      const iva = document.getElementById('filterIVA').value;
      const rows = document.querySelectorAll('#tableTramites tr[data-search]');
      const total = rows.length; let visible=0;
      rows.forEach(r => {
        const matchQ = !q || r.getAttribute('data-search').includes(q);
        const matchI = !iva || r.getAttribute('data-iva')===iva;
        const show = matchQ && matchI;
        r.style.display = show ? '' : 'none';
        if(show) visible++;
      });
      document.getElementById('resultsTra').textContent = `Mostrando ${visible} de ${total}`;
      document.getElementById('noResultsTra').style.display = visible? 'none':'block';
      document.getElementById('tableTramites').parentElement.style.display = visible? 'block':'none';
    }
    function openCreateModalTramite(){
      document.getElementById('modalTramiteTitle').textContent='Nuevo Trámite';
      document.getElementById('formTramite').action='{{ route("admin.tramites.store") }}';
      document.getElementById('tramiteMethod').value='POST';
      document.getElementById('formTramite').reset();
      openModal('modalTramite');
    }
    function editTramite(item){
      document.getElementById('modalTramiteTitle').textContent='Editar Trámite';
      document.getElementById('formTramite').action=`/admin/tramites/${item.id}`;
      document.getElementById('tramiteMethod').value='PUT';
      tra_clave_dep.value=item.clave_dependencia_siglas; tra_clave.value=item.clave_tramite; tra_variante.value=item.variante;
      tra_uso_reservado.value=item.tramite_usoreservado || ''; tra_descripcion.value=item.descripcion; tra_fundamento.value=item.fundamento_legal || '';
      tra_vigencia_de.value=item.vigencia_tramite_de || ''; tra_vigencia.value=item.vigencia_tramite_al || ''; tra_vigencia_linea.value=item.vigencia_lineacaptura || '';
      tra_tipo_vigencia.value=item.tipo_vigencia || ''; tra_clave_contable.value=item.clave_contable || ''; tra_obligatorio.value=item.obligatorio || '';
      tra_agrupador.value=item.agrupador || ''; tra_tipo_agrupador.value=item.tipo_agrupador || ''; tra_clave_periodicidad.value=item.clave_periodicidad || 'N'; tra_clave_periodo.value=item.clave_periodo || '099';
      tra_nombre_monto.value=item.nombre_monto || ''; tra_variable.value=item.variable || '';
      tra_cuota.value=item.cuota; tra_iva.checked = item.iva == 1;
      tra_actualizacion.value=item.actualizacion || ''; tra_recargos.value=item.recargos || ''; tra_multa.value=item.multa_correccionfiscal || ''; tra_compensacion.value=item.compensacion || ''; tra_saldo_favor.value=item.saldo_favor || '';
      openModal('modalTramite');
    }
    function deleteTramite(id){
      ensureDeleteModal(); document.getElementById('formDelete').action=`/admin/tramites/${id}`; openModal('modalDelete');
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
