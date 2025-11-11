<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Perfil - Biblioteca CECyTE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .profile-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .profile-header {
            background: linear-gradient(45deg, #b8b500ff, #efcb55ff);
            color: white;
            border-radius: 15px 15px 0 0;
        }
        .form-control:focus {
            border-color: #b8b500ff;
            box-shadow: 0 0 0 0.2rem rgba(230, 208, 13, 0.25);
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
                <a class="nav-link" href="{{ route('profile.show') }}">Mi Perfil</a>
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
                        <h4 class="mb-0">Editar Perfil</h4>
                    </div>
                    <div class="card-body p-4">
                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('profile.update') }}">
                            @csrf
                            @method('PUT')

                            <!-- Información Personal -->
                            <h6 class="text-muted mb-3">Información Personal</h6>
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label for="nombre" class="form-label">Nombre *</label>
                                    <input type="text" class="form-control" id="nombre" name="nombre" 
                                           value="{{ old('nombre', $usuario->nombre) }}" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="apellido_paterno" class="form-label">Apellido Paterno *</label>
                                    <input type="text" class="form-control" id="apellido_paterno" name="apellido_paterno" 
                                           value="{{ old('apellido_paterno', $usuario->apellido_paterno) }}" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="apellido_materno" class="form-label">Apellido Materno *</label>
                                    <input type="text" class="form-control" id="apellido_materno" name="apellido_materno" 
                                           value="{{ old('apellido_materno', $usuario->apellido_materno) }}" required>
                                </div>
                            </div>

                            <!-- Información de Contacto -->
                            <h6 class="text-muted mb-3 mt-4">Información de Contacto</h6>
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <label for="email" class="form-label">Email *</label>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           value="{{ old('email', $usuario->email) }}" required>
                                </div>
                            </div>

                            <!-- Campos no editables -->
                            <h6 class="text-muted mb-3 mt-4">Información Académica (No editable)</h6>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Número de Control</label>
                                    <input type="text" class="form-control" value="{{ $usuario->numero_control }}" disabled>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Carrera</label>
                                    <input type="text" class="form-control" value="{{ $usuario->carrera }}" disabled>
                                </div>
                            </div>

                            <!-- Cambiar Contraseña -->
                            <h6 class="text-muted mb-3 mt-4">Cambiar Contraseña</h6>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="current_password" class="form-label">Contraseña Actual</label>
                                    <input type="password" class="form-control" id="current_password" name="current_password" 
                                           placeholder="Solo si deseas cambiar contraseña">
                                </div>
                            </div>
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label for="new_password" class="form-label">Nueva Contraseña</label>
                                    <input type="password" class="form-control" id="new_password" name="new_password" 
                                           placeholder="Mínimo 6 caracteres">
                                </div>
                                <div class="col-md-6">
                                    <label for="new_password_confirmation" class="form-label">Confirmar Nueva Contraseña</label>
                                    <input type="password" class="form-control" id="new_password_confirmation" 
                                           name="new_password_confirmation" placeholder="Repite la nueva contraseña">
                                </div>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-success">Guardar Cambios</button>
                                <a href="{{ route('profile.show') }}" class="btn btn-outline-secondary">Cancelar</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>