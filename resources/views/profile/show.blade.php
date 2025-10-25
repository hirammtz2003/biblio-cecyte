<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil - Biblioteca CECyTE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .profile-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .profile-header {
            background: linear-gradient(45deg, #6c5ce7, #a29bfe);
            color: white;
            border-radius: 15px 15px 0 0;
        }
        .info-label {
            font-weight: 600;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="{{ route('dashboard') }}">Biblioteca CECyTE</a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="{{ route('dashboard') }}">Dashboard</a>
                <span class="navbar-text me-3">
                    {{ Auth::user()->nombre }}
                </span>
                <form method="POST" action="{{ route('logout') }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-outline-light btn-sm">Cerrar Sesión</button>
                </form>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card profile-card">
                    <div class="card-header profile-header py-3">
                        <h4 class="mb-0">Mi Perfil</h4>
                    </div>
                    <div class="card-body p-4">
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <div class="row mb-3">
                            <div class="col-md-4 info-label">Nombre completo:</div>
                            <div class="col-md-8">{{ $usuario->getNombreCompleto() }}</div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4 info-label">Número de control:</div>
                            <div class="col-md-8">{{ $usuario->numero_control }}</div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4 info-label">Carrera:</div>
                            <div class="col-md-8">{{ $usuario->carrera }}</div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4 info-label">Email:</div>
                            <div class="col-md-8">{{ $usuario->email }}</div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4 info-label">Tipo de usuario:</div>
                            <div class="col-md-8">
                                <span class="badge bg-{{ $usuario->isAdmin() ? 'danger' : 'primary' }}">
                                    {{ $usuario->tipo_usuario }}
                                </span>
                            </div>
                        </div>

                        <div class="d-flex gap-2 mt-4">
                            <a href="{{ route('profile.edit') }}" class="btn btn-primary">
                                Editar Perfil
                            </a>
                            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                                Volver al Dashboard
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>