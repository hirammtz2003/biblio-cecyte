<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Biblioteca CECyTE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .navbar-brand {
            font-weight: bold;
        }
        .welcome-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 3rem 0;
            margin-bottom: 2rem;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
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
            <span class="navbar-text me-3">
                Bienvenido, {{ Auth::user()->nombre }}
            </span>
            <form method="POST" action="{{ route('logout') }}" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-outline-light btn-sm">Cerrar Sesión</button>
            </form>
        </div>
    </nav>

    <!-- Welcome Section -->
    <div class="welcome-section">
        <div class="container text-center">
            <h1>Biblioteca Virtual CECyTE</h1>
            <p class="lead">Sistema de gestión bibliotecaria</p>
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

        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <h5 class="card-title">📚 Catálogo de Libros</h5>
                        <p class="card-text">Explora nuestra colección de libros disponibles.</p>
                        <button class="btn btn-primary" disabled>Próximamente</button>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <h5 class="card-title">🔍 Buscar Libros</h5>
                        <p class="card-text">Encuentra libros por título, autor o categoría.</p>
                        <button class="btn btn-primary" disabled>Próximamente</button>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <h5 class="card-title">📖 Mis Préstamos</h5>
                        <p class="card-text">Consulta tus libros prestados y fechas de entrega.</p>
                        <button class="btn btn-primary" disabled>Próximamente</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- User Info -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0">Información del Usuario</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Nombre:</strong> {{ Auth::user()->getNombreCompleto() }}</p>
                        <p><strong>Número de Control:</strong> {{ Auth::user()->numero_control }}</p>
                        <p><strong>Email:</strong> {{ Auth::user()->email }}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Carrera:</strong> {{ Auth::user()->carrera }}</p>
                        <p><strong>Tipo de Usuario:</strong> 
                            <span class="badge bg-{{ Auth::user()->isAdmin() ? 'danger' : 'primary' }}">
                                {{ Auth::user()->tipo_usuario }}
                            </span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>