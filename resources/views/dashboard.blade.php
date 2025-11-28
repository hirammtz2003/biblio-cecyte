<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Biblioteca CECyTE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .navbar-brand {
            font-weight: bold;
            font-size: 1.5rem;
        }
        .welcome-section {
            background: linear-gradient(135deg, #4e0d0dff 0%, #a01508 100%);
            color: white;
            padding: 3rem 0;
            margin-bottom: 2rem;
        }
        .search-section {
            background-color: #f8f9fa;
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .book-card {
            transition: transform 0.2s;
            height: 100%;
        }
        .book-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        .book-cover {
            height: 200px;
            object-fit: cover;
            background: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6c757d;
        }
        .book-title {
            font-size: 0.9rem;
            font-weight: bold;
            height: 40px;
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
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
                @if(Auth::user()->isAdmin())
                    <a class="nav-link" href="{{ route('admin.usuarios.index') }}">
                        <i class="fas fa-users-cog"></i> Administración
                    </a>
                @endif
                @if(Auth::user()->tipo_usuario === 'Docente')
                    <a class="nav-link" href="{{ route('docente.libros.index') }}">
                        <i class="fas fa-book"></i> Mis Libros
                    </a>
                @endif
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

    <!-- Welcome Section -->
    <div class="welcome-section">
        <div class="container text-center">
            <h1>Biblioteca Virtual CECyTEZ Río Grande</h1>
            <p class="lead">Sistema de consulta digital</p>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Mensaje de bienvenida -->
        @if(empty($busqueda) && empty($carrera) && empty($semestre))
        <div class="alert alert-info">
            <h5>¡Bienvenido a la Biblioteca virtual oficial del CECyTEZ Plantel Río Grande!</h5>
            <p class="mb-0">
                Aquí podrás encontrar material bibliográfico y didáctico que te ayudará a realizar tus tareas y actividades escolares. 
                Ingresa en la barra de búsqueda el título de un libro o palabras claves para buscar; también puedes usar los filtros 
                disponibles para agilizar tu búsqueda.
            </p>
        </div>
        @endif
        
        <!-- Barra de Búsqueda -->
        <div class="search-section">
            <form method="GET" action="{{ route('busqueda.buscar') }}" id="searchForm">
                <div class="row g-3">
                    <div class="col-12">
                        <div class="input-group input-group-lg">
                            <input type="text" 
                                   name="busqueda" 
                                   class="form-control" 
                                   placeholder="Buscar por título, autor, descripción, ISBN o materia..."
                                   value="{{ $busqueda ?? '' }}"
                                   aria-label="Buscar libros">
                            <button class="btn btn-primary" type="submit">
                                <i class="fas fa-search"></i> Buscar
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Filtros -->
                <div class="row g-3 mt-2">
                    <div class="col-md-6">
                        <label class="form-label">Carrera:</label>
                        <select name="carrera" class="form-select">
                            <option value="">Todas las carreras</option>
                            <option value="Soporte y Mantenimiento de Equipo de Cómputo" {{ ($carrera ?? '') == 'Soporte y Mantenimiento de Equipo de Cómputo' ? 'selected' : '' }}>
                                Soporte y Mantenimiento de Equipo de Cómputo
                            </option>
                            <option value="Enfermería General" {{ ($carrera ?? '') == 'Enfermería General' ? 'selected' : '' }}>
                                Enfermería General
                            </option>
                            <option value="Ventas" {{ ($carrera ?? '') == 'Ventas' ? 'selected' : '' }}>
                                Ventas
                            </option>
                            <option value="Diseño Gráfico Digital" {{ ($carrera ?? '') == 'Diseño Gráfico Digital' ? 'selected' : '' }}>
                                Diseño Gráfico Digital
                            </option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Semestre:</label>
                        <select name="semestre" class="form-select">
                            <option value="">Todos los semestres</option>
                            @foreach(['1°', '2°', '3°', '4°', '5°', '6°'] as $sem)
                                <option value="{{ $sem }}" {{ ($semestre ?? '') == $sem ? 'selected' : '' }}>
                                    {{ $sem }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Botones de acción -->
                <div class="row mt-3">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Buscar
                        </button>
                        <button type="button" class="btn btn-outline-secondary" onclick="limpiarBusqueda()">
                            <i class="fas fa-times"></i> Limpiar
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Resultados de búsqueda (solo se muestra en la página de resultados) -->
        @if(isset($libros))
            @include('busqueda.partials.resultados')
        @endif

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function limpiarBusqueda() {
            document.getElementById('searchForm').reset();
            window.location.href = "{{ route('dashboard') }}";
        }
    </script>
</body>
</html>