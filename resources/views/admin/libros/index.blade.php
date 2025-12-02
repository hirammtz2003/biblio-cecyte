<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Libros - Administrador - Biblioteca CECyTE</title>
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
            background: linear-gradient(45deg, #2c3e50, #3498db);
            color: white;
            border-radius: 15px 15px 0 0;
        }
        .search-box {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }
        .book-card {
            border-left: 4px solid #3498db;
            transition: transform 0.2s;
        }
        .book-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .badge-admin {
            background-color: #2c3e50;
        }
        .badge-docente {
            background-color: #27ae60;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="{{ route('dashboard') }}">
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
        <div class="card admin-card">
            <div class="card-header admin-header py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-book"></i> Gestión de Libros - Administrador
                    </h4>
                    <div><!--
                        <a href="{{ route('admin.usuarios.index') }}" class="btn btn-light me-2">
                            <i class="fas fa-users"></i> Usuarios
                        </a>-->
                        <a href="{{ route('admin.libros.create') }}" class="btn btn-light">
                            <i class="fas fa-plus"></i> Subir Nuevo Libro
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body p-4">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <!-- Barra de Búsqueda y Filtros Avanzados -->
                <div class="search-box">
                    <form method="GET" action="{{ route('admin.libros.index') }}">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Buscar:</label>
                                <input type="text" name="busqueda" class="form-control" 
                                       placeholder="Título, autor, materia..." 
                                       value="{{ $busqueda }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Carrera:</label>
                                <select name="carrera" class="form-select">
                                    <option value="">Todas las carreras</option>
                                    <option value="Soporte y Mantenimiento de Equipo de Cómputo" {{ $carrera == 'Soporte y Mantenimiento de Equipo de Cómputo' ? 'selected' : '' }}>
                                        Soporte y Mantenimiento
                                    </option>
                                    <option value="Enfermería General" {{ $carrera == 'Enfermería General' ? 'selected' : '' }}>
                                        Enfermería General
                                    </option>
                                    <option value="Ventas" {{ $carrera == 'Ventas' ? 'selected' : '' }}>
                                        Ventas
                                    </option>
                                    <option value="Diseño Gráfico Digital" {{ $carrera == 'Diseño Gráfico Digital' ? 'selected' : '' }}>
                                        Diseño Gráfico
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Semestre:</label>
                                <select name="semestre" class="form-select">
                                    <option value="">Todos</option>
                                    @for($i = 1; $i <= 6; $i++)
                                        <option value="{{ $i }}°" {{ $semestre == $i.'°' ? 'selected' : '' }}>
                                            {{ $i }}° Semestre
                                        </option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Docente:</label>
                                <input type="text" name="docente" class="form-control" 
                                       placeholder="Nombre o email del docente" 
                                       value="{{ $docente }}">
                            </div>
                        </div>
                        <div class="row g-3 mt-2">
                            <div class="col-md-3">
                                <label class="form-label">Ordenar por:</label>
                                <select name="orden" class="form-select">
                                    <option value="fecha_desc" {{ $orden == 'fecha_desc' ? 'selected' : '' }}>Más recientes</option>
                                    <option value="fecha_asc" {{ $orden == 'fecha_asc' ? 'selected' : '' }}>Más antiguos</option>
                                    <option value="titulo_asc" {{ $orden == 'titulo_asc' ? 'selected' : '' }}>Título A-Z</option>
                                    <option value="titulo_desc" {{ $orden == 'titulo_desc' ? 'selected' : '' }}>Título Z-A</option>
                                </select>
                            </div>
                            <div class="col-md-6 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="fas fa-search"></i> Buscar
                                </button>
                                <a href="{{ route('admin.libros.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-refresh"></i> Limpiar
                                </a>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Estadísticas -->
                <div class="row mb-4">
                    <div class="col-md-12">
                        <div class="alert alert-info">
                            <div class="row text-center">
                                <div class="col-md-4">
                                    <i class="fas fa-book fa-2x"></i>
                                    <h5 class="mt-2">{{ $totalLibros }}</h5>
                                    <small>Total de Libros</small>
                                </div>
                                <div class="col-md-4">
                                    <i class="fas fa-users fa-2x"></i>
                                    <h5 class="mt-2">{{ $docentes->count() }}</h5>
                                    <small>Docentes Activos</small>
                                </div>
                                <div class="col-md-4">
                                    <i class="fas fa-graduation-cap fa-2x"></i>
                                    <h5 class="mt-2">4</h5>
                                    <small>Carreras</small>
                                </div><!--
                                <div class="col-md-3">
                                    <i class="fas fa-folder fa-2x"></i>
                                    <h5 class="mt-2">24</h5>
                                    <small>Categorías</small>
                                </div>-->
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabla de Libros -->
                @if($libros->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Título</th>
                                    <th>Autor</th>
                                    <th>Carrera/Semestre</th>
                                    <th>Subido por</th>
                                    <th>Estadísticas</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($libros as $libro)
                                    <tr>
                                        <td>
                                            <strong>{{ $libro->titulo }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $libro->materia }}</small>
                                            <br>
                                            <small class="text-muted">{{ $libro->getTamanioFormateado() }}</small>
                                        </td>
                                        <td>{{ $libro->autor }}</td>
                                        <td>
                                            <span class="badge bg-info">{{ $libro->carrera }}</span>
                                            <span class="badge bg-primary">{{ $libro->semestre }}</span>
                                            <br>
                                            <small class="text-muted">{{ $libro->anio_publicacion }}</small>
                                        </td>
                                        <td>
                                            <span class="badge {{ $libro->usuario->tipo_usuario == 'Admin' ? 'badge-admin' : 'badge-docente' }}">
                                                {{ $libro->usuario->nombre }}
                                            </span>
                                            <br>
                                            <small class="text-muted">{{ $libro->created_at->format('d/m/Y') }}</small>
                                        </td>
                                        <td>
                                            <small>
                                                <i class="fas fa-eye text-primary"></i> {{ $libro->veces_visto }}
                                                <i class="fas fa-download text-success ms-2"></i> {{ $libro->veces_descargado }}
                                            </small>
                                            <br>
                                            @if($libro->descargable)
                                                <span class="badge bg-success">Descargable</span>
                                            @else
                                                <span class="badge bg-warning">Solo lectura</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('libro.ver', $libro->id) }}" 
                                                   class="btn btn-sm btn-outline-primary" title="Ver">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.libros.edit', $libro->id) }}" 
                                                   class="btn btn-sm btn-outline-warning" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('admin.libros.destroy', $libro->id) }}" 
                                                      method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                            title="Eliminar"
                                                            onclick="return confirm('¿Estás seguro de eliminar este libro?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginación -->
                    <div class="d-flex justify-content-center mt-4">
                        {{ $libros->appends(request()->query())->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-book fa-4x text-muted mb-3"></i>
                        <h5 class="text-muted">No se encontraron libros</h5>
                        <p class="text-muted">Intenta con otros filtros de búsqueda</p>
                        <a href="{{ route('admin.libros.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Subir Primer Libro
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>