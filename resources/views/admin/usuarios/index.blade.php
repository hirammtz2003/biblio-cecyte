<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administración de Usuarios - Biblioteca CECyTE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .navbar-brand {
            font-weight: bold;
            font-size: 1.5rem;
        }
        .admin-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .admin-header {
            background: linear-gradient(45deg, #e74c3c, #e67e22);
            color: white;
            border-radius: 15px 15px 0 0;
        }
        .stats-card {
            background: linear-gradient(45deg, #3498db, #2980b9);
            color: white;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1rem;
        }
        .user-row.editing {
            background-color: #fff3cd !important;
        }
        .badge-admin { background-color: #e74c3c; }
        .badge-docente { background-color: #f39c12; }
        .badge-alumno { background-color: #27ae60; }
        .filter-section {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand nav-brand" href="{{ route('dashboard') }}">
                <i class="fas fa-book"></i> Biblioteca CECyTE
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="{{ route('profile.show') }}">Mi Perfil</a>
                <a class="nav-link" href="{{ route('favoritos.index') }}">
                    <i class="fas fa-star"></i> Mis Favoritos
                </a>
                <span class="navbar-text me-3">
                    Bienvenido/a, {{ Auth::user()->nombre }}
                </span>
                <form method="POST" action="{{ route('logout') }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-outline-light btn-sm">Cerrar Sesión</button>
                </form>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <!-- Estadísticas -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="stats-card text-center">
                    <h4 id="total-usuarios">0</h4>
                    <p class="mb-0">Total Usuarios</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card text-center" style="background: linear-gradient(45deg, #e74c3c, #c0392b);">
                    <h4 id="total-admins">0</h4>
                    <p class="mb-0">Administradores</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card text-center" style="background: linear-gradient(45deg, #f39c12, #d35400);">
                    <h4 id="total-docentes">0</h4>
                    <p class="mb-0">Docentes</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card text-center" style="background: linear-gradient(45deg, #27ae60, #229954);">
                    <h4 id="total-alumnos">0</h4>
                    <p class="mb-0">Alumnos</p>
                </div>
            </div>
        </div>

        <div class="card admin-card">
            <div class="card-header admin-header py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-users-cog"></i> Administración de Usuarios
                    </h4>
                    <span class="badge bg-light text-dark">
                        {{ $totalUsuarios }} usuario(s) encontrado(s)
                    </span>
                </div>
            </div>
            <div class="card-body p-4">
                <!-- Filtros -->
                <div class="filter-section">
                    <form method="GET" action="{{ route('admin.usuarios.index') }}" id="filterForm">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Buscar:</label>
                                <div class="input-group">
                                    <input type="text" name="busqueda" class="form-control" 
                                        placeholder="Nombre, apellidos, email..." 
                                        value="{{ $busqueda }}">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Filtrar por Tipo:</label>
                                <select name="tipo" class="form-select" onchange="document.getElementById('filterForm').submit()">
                                    <option value="todos" {{ $tipoFiltro == 'todos' ? 'selected' : '' }}>Todos los tipos</option>
                                    <option value="Administrador" {{ $tipoFiltro == 'Administrador' ? 'selected' : '' }}>Administradores</option>
                                    <option value="Docente" {{ $tipoFiltro == 'Docente' ? 'selected' : '' }}>Docentes</option>
                                    <option value="Alumno" {{ $tipoFiltro == 'Alumno' ? 'selected' : '' }}>Alumnos</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Filtrar por Carrera:</label>
                                <select name="carrera" class="form-select" onchange="document.getElementById('filterForm').submit()">
                                    <option value="todas" {{ $carreraFiltro == 'todas' ? 'selected' : '' }}>Todas las carreras</option>
                                    @foreach(['Soporte y Mantenimiento de Equipo de Cómputo', 'Enfermería General', 'Ventas', 'Diseño Gráfico Digital'] as $carrera)
                                        <option value="{{ $carrera }}" {{ $carreraFiltro == $carrera ? 'selected' : '' }}>
                                            {{ $carrera }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Ordenar por:</label>
                                <select name="orden" class="form-select" onchange="document.getElementById('filterForm').submit()">
                                    <option value="registro_desc" {{ $orden == 'registro_desc' ? 'selected' : '' }}>Más recientes primero</option>
                                    <option value="registro_asc" {{ $orden == 'registro_asc' ? 'selected' : '' }}>Más antiguos primero</option>
                                    <option value="nombre_asc" {{ $orden == 'nombre_asc' ? 'selected' : '' }}>Nombre A-Z</option>
                                    <option value="nombre_desc" {{ $orden == 'nombre_desc' ? 'selected' : '' }}>Nombre Z-A</option>
                                </select>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Tabla de Usuarios -->
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Nombre Completo</th>
                                <th>Número de Control</th>
                                <th>Email</th>
                                <th>Carrera</th>
                                <th>Tipo de Usuario</th>
                                <th>Fecha Registro</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($usuarios as $usuario)
                                <tr id="user-{{ $usuario->id }}" class="user-row">
                                    <td>{{ $usuario->getNombreCompleto() }}</td>
                                    <td>{{ $usuario->numero_control }}</td>
                                    <td>{{ $usuario->email }}</td>
                                    <td>{{ $usuario->carrera }}</td>
                                    <td>
                                        <span class="badge badge-{{ strtolower($usuario->tipo_usuario) }}">
                                            {{ $usuario->tipo_usuario }}
                                        </span>
                                    </td>
                                    <td>{{ $usuario->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <div class="btn-group">
                                            <button class="btn btn-sm btn-outline-primary edit-usuario-btn" 
                                                    data-user-id="{{ $usuario->id }}"
                                                    data-current-tipo="{{ $usuario->tipo_usuario }}"
                                                    data-current-carrera="{{ $usuario->carrera }}"
                                                    data-user-name="{{ $usuario->getNombreCompleto() }}">
                                                <i class="fas fa-edit"></i> Editar
                                            </button>
                                            @if($usuario->id !== Auth::id())
                                                <button class="btn btn-sm btn-outline-danger delete-usuario-btn" 
                                                        data-user-id="{{ $usuario->id }}"
                                                        data-user-name="{{ $usuario->getNombreCompleto() }}">
                                                    <i class="fas fa-trash"></i> Eliminar
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($usuarios->isEmpty())
                    <div class="text-center py-4">
                        <i class="fas fa-users fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No se encontraron usuarios</h5>
                        <p class="text-muted">Intenta con otros filtros</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Modal para editar usuario -->
    <div class="modal fade" id="editUsuarioModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Editar Usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="editUsuarioForm">
                        @csrf
                        <input type="hidden" name="user_id" id="edit_user_id">
                        
                        <div class="mb-3">
                            <label class="form-label">Usuario:</label>
                            <p class="form-control-plaintext fw-bold" id="edit_user_name"></p>
                        </div>
                        
                        <div class="mb-3">
                            <label for="tipo_usuario" class="form-label">Tipo de Usuario:</label>
                            <select name="tipo_usuario" id="tipo_usuario" class="form-select" required>
                                <option value="Alumno">Alumno</option>
                                <option value="Docente">Docente</option>
                                <option value="Administrador">Administrador</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="carrera" class="form-label">Carrera:</label>
                            <select name="carrera" id="carrera" class="form-select" required>
                                <option value="Soporte y Mantenimiento de Equipo de Cómputo">Soporte y Mantenimiento de Equipo de Cómputo</option>
                                <option value="Enfermería General">Enfermería General</option>
                                <option value="Ventas">Ventas</option>
                                <option value="Diseño Gráfico Digital">Diseño Gráfico Digital</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="admin_password" class="form-label">Tu Contraseña de Administrador:</label>
                            <input type="password" name="admin_password" id="admin_password" class="form-control" required>
                            <div class="form-text">Por seguridad, ingresa tu contraseña para confirmar los cambios.</div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="saveUsuarioBtn">Guardar Cambios</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para eliminar usuario -->
    <div class="modal fade" id="deleteUsuarioModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Eliminar Usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="deleteUsuarioForm">
                        @csrf
                        <input type="hidden" name="user_id" id="delete_user_id">
                        
                        <div class="mb-3">
                            <p>¿Estás seguro de que deseas eliminar al usuario <strong id="delete_user_name"></strong>?</p>
                            <p class="text-danger"><small>Esta acción no se puede deshacer.</small></p>
                        </div>
                        
                        <div class="mb-3">
                            <label for="delete_admin_password" class="form-label">Tu Contraseña de Administrador:</label>
                            <input type="password" name="admin_password" id="delete_admin_password" class="form-control" required>
                            <div class="form-text">Por seguridad, ingresa tu contraseña para confirmar la eliminación.</div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Eliminar Usuario</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Cargar estadísticas
        document.addEventListener('DOMContentLoaded', function() {
            fetch('{{ route("admin.estadisticas") }}')
                .then(response => response.json())
                .then(data => {
                    document.getElementById('total-usuarios').textContent = data.total;
                    document.getElementById('total-admins').textContent = data.admins;
                    document.getElementById('total-docentes').textContent = data.docentes;
                    document.getElementById('total-alumnos').textContent = data.alumnos;
                });

            // Manejar edición de usuario
            document.querySelectorAll('.edit-usuario-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const userId = this.dataset.userId;
                    const currentTipo = this.dataset.currentTipo;
                    const currentCarrera = this.dataset.currentCarrera;
                    const userName = this.dataset.userName;
                    
                    document.getElementById('edit_user_id').value = userId;
                    document.getElementById('edit_user_name').textContent = userName;
                    document.getElementById('tipo_usuario').value = currentTipo;
                    document.getElementById('carrera').value = currentCarrera;
                    document.getElementById('admin_password').value = '';
                    
                    const modal = new bootstrap.Modal(document.getElementById('editUsuarioModal'));
                    modal.show();
                });
            });

            // Manejar eliminación de usuario
            document.querySelectorAll('.delete-usuario-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const userId = this.dataset.userId;
                    const userName = this.dataset.userName;
                    
                    document.getElementById('delete_user_id').value = userId;
                    document.getElementById('delete_user_name').textContent = userName;
                    document.getElementById('delete_admin_password').value = '';
                    
                    const modal = new bootstrap.Modal(document.getElementById('deleteUsuarioModal'));
                    modal.show();
                });
            });

            // Guardar cambios de edición
            document.getElementById('saveUsuarioBtn').addEventListener('click', function() {
                const formData = new FormData(document.getElementById('editUsuarioForm'));
                const userId = formData.get('user_id');
                
                fetch(`/admin/usuarios/${userId}`, {
                    method: 'PUT',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        tipo_usuario: formData.get('tipo_usuario'),
                        carrera: formData.get('carrera'),
                        admin_password: formData.get('admin_password')
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Actualizar la fila en la tabla
                        const userRow = document.getElementById(`user-${userId}`);
                        
                        // Actualizar tipo de usuario
                        const tipoCell = userRow.querySelector('td:nth-child(5)');
                        const badge = tipoCell.querySelector('.badge');
                        
                        badge.className = `badge badge-${data.usuario.tipo_usuario.toLowerCase()}`;
                        badge.textContent = data.usuario.tipo_usuario;
                        
                        // Actualizar carrera
                        const carreraCell = userRow.querySelector('td:nth-child(4)');
                        carreraCell.textContent = data.usuario.carrera;
                        
                        // Actualizar datos en los botones
                        const editBtn = userRow.querySelector('.edit-usuario-btn');
                        editBtn.dataset.currentTipo = data.usuario.tipo_usuario;
                        editBtn.dataset.currentCarrera = data.usuario.carrera;
                        
                        // Cerrar modal y mostrar mensaje
                        bootstrap.Modal.getInstance(document.getElementById('editUsuarioModal')).hide();
                        alert('Usuario actualizado correctamente.');
                        
                        // Recargar estadísticas
                        location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error al actualizar el usuario.');
                });
            });

            // Confirmar eliminación
            document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
                const formData = new FormData(document.getElementById('deleteUsuarioForm'));
                const userId = formData.get('user_id');
                
                fetch(`/admin/usuarios/${userId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        admin_password: formData.get('admin_password')
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Remover la fila de la tabla
                        const userRow = document.getElementById(`user-${userId}`);
                        userRow.remove();
                        
                        // Actualizar contador
                        const badgeCount = document.querySelector('.admin-header .badge');
                        const currentCount = parseInt(badgeCount.textContent);
                        badgeCount.textContent = `${currentCount - 1} usuario(s) encontrado(s)`;
                        
                        // Cerrar modal y mostrar mensaje
                        bootstrap.Modal.getInstance(document.getElementById('deleteUsuarioModal')).hide();
                        alert('Usuario eliminado correctamente.');
                        
                        // Recargar estadísticas
                        location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error al eliminar el usuario.');
                });
            });
        });
    </script>
</body>
</html>