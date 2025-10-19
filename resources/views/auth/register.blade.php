<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Biblioteca CECyTE</title>
    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .register-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        .card-header {
            border-radius: 15px 15px 0 0 !important;
            background: linear-gradient(45deg, #28a745, #20c997);
        }
        .form-control:focus {
            border-color: #28a745;
            box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
        }
    </style>
</head>
<body class="d-flex align-items-center">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card register-card">
                    <div class="card-header text-white text-center py-4">
                        <h4 class="mb-1">Registro - Biblioteca CECyTE</h4>
                        <small class="opacity-75">Crea tu cuenta de estudiante</small>
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

                        <form method="POST" action="{{ route('register') }}">
                            @csrf
                            
                            <!-- Datos Personales -->
                            <h6 class="text-muted mb-3">Datos Personales</h6>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="nombre" class="form-label">Nombre *</label>
                                    <input type="text" class="form-control" id="nombre" name="nombre" 
                                           value="{{ old('nombre') }}" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="apellido_paterno" class="form-label">Apellido Paterno *</label>
                                    <input type="text" class="form-control" id="apellido_paterno" name="apellido_paterno" 
                                           value="{{ old('apellido_paterno') }}" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="apellido_materno" class="form-label">Apellido Materno *</label>
                                    <input type="text" class="form-control" id="apellido_materno" name="apellido_materno" 
                                           value="{{ old('apellido_materno') }}" required>
                                </div>
                            </div>

                            <!-- Datos Académicos -->
                            <h6 class="text-muted mb-3 mt-4">Datos Académicos</h6>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="carrera" class="form-label">Carrera *</label>
                                    <select class="form-select" id="carrera" name="carrera" required>
                                        <option value="">Selecciona tu carrera</option>
                                        <option value="Soporte y Mantenimiento de Equipo de Cómputo" {{ old('carrera') == 'Soporte y Mantenimiento de Equipo de Cómputo' ? 'selected' : '' }}>
                                            Soporte y Mantenimiento de Equipo de Cómputo
                                        </option>
                                        <option value="Enfermería General" {{ old('carrera') == 'Enfermería General' ? 'selected' : '' }}>
                                            Enfermería General
                                        </option>
                                        <option value="Ventas" {{ old('carrera') == 'Ventas' ? 'selected' : '' }}>
                                            Ventas
                                        </option>
                                        <option value="Diseño Gráfico Digital" {{ old('carrera') == 'Diseño Gráfico Digital' ? 'selected' : '' }}>
                                            Diseño Gráfico Digital
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="numero_control" class="form-label">Número de Control *</label>
                                    <input type="text" class="form-control" id="numero_control" name="numero_control" 
                                           value="{{ old('numero_control') }}" required placeholder="Ej: 20240001">
                                </div>
                            </div>

                            <!-- Datos de Cuenta -->
                            <h6 class="text-muted mb-3 mt-4">Datos de Cuenta</h6>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Correo Electrónico *</label>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           value="{{ old('email') }}" required placeholder="ejemplo@cecyte.edu.mx">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="password" class="form-label">Contraseña *</label>
                                    <input type="password" class="form-control" id="password" name="password" required 
                                           minlength="6" placeholder="Mínimo 6 caracteres">
                                </div>
                            </div>
                            <div class="mb-4">
                                <label for="password_confirmation" class="form-label">Confirmar Contraseña *</label>
                                <input type="password" class="form-control" id="password_confirmation" 
                                       name="password_confirmation" required placeholder="Repite tu contraseña">
                            </div>

                            <button type="submit" class="btn btn-success w-100 py-2">Registrarse</button>
                        </form>

                        <div class="text-center mt-3">
                            <a href="{{ route('login') }}" class="text-decoration-none">
                                ¿Ya tienes cuenta? Inicia sesión aquí
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>