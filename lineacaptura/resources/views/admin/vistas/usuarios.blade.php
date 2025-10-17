<section id="users-section" class="content-card data-section">
  <h3 class="title-content">Usuarios del sistema</h3>
  <hr>

  <div class="search-filter-container">
    <div class="search-box">
      <img src="https://img.icons8.com/ios-filled/50/858796/search.png" class="search-icon" alt="buscar">
      <input type="text" id="searchUsers" placeholder="Buscar usuarios (ID, nombre, email...)">
      <button class="clear-search" id="clearSearchUsers" onclick="clearSearchUsers()">&times;</button>
    </div>
    <span class="results-count" id="resultsUsers">Mostrando {{ $users->count() }} de {{ $users->count() }}</span>
  </div>

  <div class="table-container">
    <table class="data-table">
      <thead>
        <tr><th>ID</th><th>Nombre</th><th>Email</th><th>Fecha de Registro</th><th>Acciones</th></tr>
      </thead>
      <tbody id="tableUsers">
        @forelse($users as $user)
          <tr data-search="{{ strtolower($user->id.' '.$user->name.' '.$user->email.' '.($user->created_at ? $user->created_at->format('d/m/Y') : '')) }}">
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
    <div id="noResultsUsers" class="no-results" style="display:none;">❌ No se encontraron resultados</div>
  </div>

  {{-- MODAL Usuario --}}
  <div id="modalUser" class="modal">
    <div class="modal-content">
      <div class="modal-header">
        <h3>Editar Usuario</h3>
        <span class="close" onclick="closeModal('modalUser')">&times;</span>
      </div>
      <form id="formUser" method="POST">
        @csrf <input type="hidden" name="_method" value="PUT">
        <div class="modal-body">
          <div class="form-group"><label>Nombre *</label><input class="form-control" id="user_name" name="name" required></div>
          <div class="form-group"><label>Email *</label><input type="email" class="form-control" id="user_email" name="email" required></div>
          <div class="form-group"><label>Nueva contraseña (opcional)</label><input type="password" class="form-control" id="user_password" name="password" minlength="8"></div>
          <div class="form-group"><label>Confirmar Contraseña</label><input type="password" class="form-control" id="user_password_confirmation" name="password_confirmation" minlength="8"></div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger" onclick="closeModal('modalUser')">Cancelar</button>
          <button type="submit" class="btn btn-success">Guardar</button>
        </div>
      </form>
    </div>
  </div>

  {{-- JS de esta sección --}}
  <script>
    const usrInput = document.getElementById('searchUsers');
    if(usrInput){ usrInput.addEventListener('input', e => { filterUsers(e.target.value); toggleClearButton('clearSearchUsers', e.target.value); }); }
    function clearSearchUsers(){ usrInput.value=''; filterUsers(''); toggleClearButton('clearSearchUsers',''); }
    function filterUsers(q){
      const rows = document.querySelectorAll('#tableUsers tr[data-search]'); const total=rows.length; let visible=0;
      q=(q||'').toLowerCase().trim();
      rows.forEach(r => { const show = !q || r.getAttribute('data-search').includes(q); r.style.display = show?'':'none'; if(show) visible++; });
      document.getElementById('resultsUsers').textContent = `Mostrando ${visible} de ${total}`;
      document.getElementById('noResultsUsers').style.display = visible? 'none':'block';
      document.getElementById('tableUsers').parentElement.style.display = visible? 'block':'none';
    }
    function editUser(user){
      document.getElementById('formUser').action=`/admin/usuarios/${user.id}`;
      user_name.value=user.name; user_email.value=user.email; user_password.value=''; user_password_confirmation.value='';
      openModal('modalUser');
    }
    function deleteUser(id){
      ensureDeleteModal(); document.getElementById('formDelete').action=`/admin/usuarios/${id}`; openModal('modalDelete');
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
