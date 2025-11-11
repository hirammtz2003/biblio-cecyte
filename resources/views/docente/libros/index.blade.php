<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Libros Subidos - Biblioteca CECyTE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .docente-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .docente-header {
            background: linear-gradient(45deg, #e46612ff, #e64b24ff);
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
            border-left: 4px solid #e46612ff;
            transition: transform 0.2s;
        }
        .book-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="{{ route('dashboard') }}">Biblioteca CECyTE</a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="{{ route('dashboard') }}">Dashboard</a>
                <a class="nav-link" href="{{ route('profile.show') }}">Mi Perfil</a>
                @if(Auth::user()->isAdmin())
                    <a class="nav-link" href="{{ route('admin.usuarios.index') }}">
                        <i class="fas fa-users-cog"></i> Administración
                    </a>
                @endif
                @if(Auth::user()->tipo_usuario === 'Docente')
                    <a class="nav-link active" href="{{ route('docente.libros.index') }}">
                        <i class="fas fa-book"></i> Mis Libros
                    </a>
                @endif
                <span class="navbar-text me-3">{{ Auth::user()->nombre }}</span>
                <form method="POST" action="{{ route('logout') }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-outline-light btn-sm">Cerrar Sesión</button>
                </form>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="card docente-card">
            <div class="card-header docente-header py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-book"></i> Gestión de Libros
                    </h4>
                    <a href="{{ route('docente.libros.create') }}" class="btn btn-light">
                        <i class="fas fa-plus"></i> Subir Nuevo Libro
                    </a>
                </div>
            </div>
            <div class="card-body p-4">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <!-- Barra de Búsqueda y Filtros -->
                <div class="search-box">
                    <form method="GET" action="{{ route('docente.libros.index') }}">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Buscar:</label>
                                <div class="input-group">
                                    <input type="text" name="busqueda" class="form-control" 
                                           placeholder="Buscar por título, autor, materia..." 
                                           value="{{ $busqueda }}">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Ordenar por:</label>
                                <select name="orden" class="form-select" onchange="this.form.submit()">
                                    <option value="fecha_desc" {{ $orden == 'fecha_desc' ? 'selected' : '' }}>Más recientes primero</option>
                                    <option value="fecha_asc" {{ $orden == 'fecha_asc' ? 'selected' : '' }}>Más antiguos primero</option>
                                    <option value="titulo_asc" {{ $orden == 'titulo_asc' ? 'selected' : '' }}>Título A-Z</option>
                                    <option value="titulo_desc" {{ $orden == 'titulo_desc' ? 'selected' : '' }}>Título Z-A</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">&nbsp;</label>
                                <a href="{{ route('docente.libros.index') }}" class="btn btn-outline-secondary w-100">
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
                            <i class="fas fa-info-circle"></i>
                            Tienes <strong>{{ $totalLibros }}</strong> libro(s) en tu biblioteca.
                        </div>
                    </div>
                </div>

                <!-- Lista de Libros -->
                @if($libros->count() > 0)
                    <div class="row">
                        @foreach($libros as $libro)
                            <div class="col-md-6 mb-4">
                                <div class="card book-card h-100">
                                    <div class="card-body">
                                        <h5 class="card-title">{{ $libro->titulo }}</h5>
                                        <p class="card-text">
                                            <strong>Autor:</strong> {{ $libro->autor }}<br>
                                            <strong>Materia:</strong> {{ $libro->materia }}<br>
                                            <strong>Carrera:</strong> {{ $libro->carrera }} - {{ $libro->semestre }}<br>
                                            <strong>Año:</strong> {{ $libro->anio_publicacion }}<br>
                                            <strong>Tamaño:</strong> {{ $libro->getTamanioFormateado() }}<br>
                                            <strong>Subido:</strong> {{ $libro->created_at->format('d/m/Y') }}
                                        </p>
                                        @if($libro->descripcion)
                                            <p class="card-text">
                                                <small class="text-muted">{{ Str::limit($libro->descripcion, 100) }}</small>
                                            </p>
                                        @endif
                                    </div>
                                    <div class="card-footer bg-transparent">
                                        <div class="btn-group w-100">
                                            <a href="{{ route('libro.ver', $libro->id) }}" 
                                               class="btn btn-outline-primary btn-sm" title="Ver libro">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('docente.libros.download', $libro->id) }}" 
                                               class="btn btn-outline-primary btn-sm" title="Descargar">
                                                <i class="fas fa-download"></i>
                                            </a>
                                            <a href="{{ route('docente.libros.edit', $libro->id) }}" 
                                               class="btn btn-outline-warning btn-sm" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('docente.libros.destroy', $libro->id) }}" 
                                                  method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger btn-sm" 
                                                        title="Eliminar"
                                                        onclick="return confirm('¿Estás seguro de eliminar este libro?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Paginación -->
                    <div class="d-flex justify-content-center mt-4">
                        {{ $libros->appends(request()->query())->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-book fa-4x text-muted mb-3"></i>
                        <h5 class="text-muted">No tienes libros subidos</h5>
                        <p class="text-muted">Comienza subiendo tu primer libro PDF</p>
                        <a href="{{ route('docente.libros.create') }}" class="btn btn-primary">
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