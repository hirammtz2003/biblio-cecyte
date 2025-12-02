<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Libro - Biblioteca CECyTE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .navbar-brand {
            font-weight: bold;
            font-size: 1.5rem;
        }
        .edit-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .edit-header {
            background: linear-gradient(45deg, #f39c12, #e67e22);
            color: white;
            border-radius: 15px 15px 0 0;
        }
        .file-preview {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 1.5rem;
            border-left: 4px solid #f39c12;
        }
        .form-control:focus, .form-select:focus {
            border-color: #f39c12;
            box-shadow: 0 0 0 0.2rem rgba(243, 156, 18, 0.25);
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

    <div class="container mt-4">
        <div class="card edit-card">
            <div class="card-header edit-header py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-edit"></i> Editar Libro: {{ $libro->titulo }}
                    </h4>
                    <a href="{{ route('admin.libros.index') }}" class="btn btn-light">
                        <i class="fas fa-arrow-left"></i> Volver
                    </a>
                </div>
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

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <!-- Información del Archivo Actual -->
                <div class="file-preview mb-4">
                    <h6 class="text-warning mb-3">
                        <i class="fas fa-file-pdf"></i> Archivo Actual
                    </h6>
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-file-pdf text-danger fa-2x"></i>
                            <div class="ms-3 d-inline-block">
                                <strong>{{ $libro->nombre_archivo }}</strong>
                                <br>
                                <small class="text-muted">
                                    Tamaño: {{ $libro->getTamanioFormateado() }} • 
                                    Subido: {{ $libro->created_at->format('d/m/Y H:i') }}
                                </small>
                            </div>
                        </div>
                        <div>
                            <a href="{{ route('docente.libros.download', $libro->id) }}" 
                               class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-download"></i> Descargar
                            </a>
                        </div>
                    </div>
                    <div class="mt-2">
                        <small class="text-muted">
                            <i class="fas fa-info-circle"></i>
                            El archivo PDF no se puede modificar. Si necesitas cambiar el archivo, 
                            elimina este libro y crea uno nuevo.
                        </small>
                    </div>
                </div>

                <form method="POST" action="{{ route('admin.libros.update', $libro->id) }}">
                    @csrf
                    @method('PUT')

                    <h5 class="text-primary mb-3">
                        <i class="fas fa-info-circle"></i> Información del Libro
                    </h5>
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="titulo" class="form-label">Título del Libro *</label>
                            <input type="text" class="form-control" id="titulo" name="titulo" 
                                   value="{{ old('titulo', $libro->titulo) }}" required maxlength="255">
                        </div>
                        
                        <div class="col-md-6">
                            <label for="autor" class="form-label">Autor(es) *</label>
                            <input type="text" class="form-control" id="autor" name="autor" 
                                   value="{{ old('autor', $libro->autor) }}" required maxlength="255">
                        </div>
                        
                        <div class="col-md-4">
                            <label for="anio_publicacion" class="form-label">Año de Publicación *</label>
                            <input type="number" class="form-control" id="anio_publicacion" name="anio_publicacion" 
                                   value="{{ old('anio_publicacion', $libro->anio_publicacion) }}" 
                                   min="1900" max="{{ date('Y') + 1 }}" required>
                        </div>
                        
                        <div class="col-md-4">
                            <label for="isbn" class="form-label">ISBN (Opcional)</label>
                            <input type="text" class="form-control" id="isbn" name="isbn" 
                                   value="{{ old('isbn', $libro->isbn) }}" maxlength="20">
                        </div>
                        
                        <div class="col-md-4">
                            <label for="materia" class="form-label">Materia *</label>
                            <input type="text" class="form-control" id="materia" name="materia" 
                                   value="{{ old('materia', $libro->materia) }}" required maxlength="150">
                        </div>
                        
                        <div class="col-md-6">
                            <label for="carrera" class="form-label">Carrera *</label>
                            <select class="form-select" id="carrera" name="carrera" required>
                                <option value="">Selecciona una carrera</option>
                                <option value="Soporte y Mantenimiento de Equipo de Cómputo" 
                                    {{ (old('carrera', $libro->carrera) == 'Soporte y Mantenimiento de Equipo de Cómputo') ? 'selected' : '' }}>
                                    Soporte y Mantenimiento de Equipo de Cómputo
                                </option>
                                <option value="Enfermería General" 
                                    {{ (old('carrera', $libro->carrera) == 'Enfermería General') ? 'selected' : '' }}>
                                    Enfermería General
                                </option>
                                <option value="Ventas" 
                                    {{ (old('carrera', $libro->carrera) == 'Ventas') ? 'selected' : '' }}>
                                    Ventas
                                </option>
                                <option value="Diseño Gráfico Digital" 
                                    {{ (old('carrera', $libro->carrera) == 'Diseño Gráfico Digital') ? 'selected' : '' }}>
                                    Diseño Gráfico Digital
                                </option>
                            </select>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="semestre" class="form-label">Semestre *</label>
                            <select class="form-select" id="semestre" name="semestre" required>
                                <option value="">Selecciona un semestre</option>
                                <option value="1°" {{ (old('semestre', $libro->semestre) == '1°') ? 'selected' : '' }}>1° Semestre</option>
                                <option value="2°" {{ (old('semestre', $libro->semestre) == '2°') ? 'selected' : '' }}>2° Semestre</option>
                                <option value="3°" {{ (old('semestre', $libro->semestre) == '3°') ? 'selected' : '' }}>3° Semestre</option>
                                <option value="4°" {{ (old('semestre', $libro->semestre) == '4°') ? 'selected' : '' }}>4° Semestre</option>
                                <option value="5°" {{ (old('semestre', $libro->semestre) == '5°') ? 'selected' : '' }}>5° Semestre</option>
                                <option value="6°" {{ (old('semestre', $libro->semestre) == '6°') ? 'selected' : '' }}>6° Semestre</option>
                            </select>
                        </div>
                        
                        <div class="col-12">
                            <label for="descripcion" class="form-label">Descripción (Opcional)</label>
                            <textarea class="form-control" id="descripcion" name="descripcion" 
                                      rows="3" maxlength="500">{{ old('descripcion', $libro->descripcion) }}</textarea>
                            <div class="form-text">
                                Breve descripción o resumen del contenido del libro (máximo 500 caracteres).
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="descargable" name="descargable" value="1" 
                                    {{ $libro->descargable ? 'checked' : '' }}>
                                <label class="form-check-label" for="descargable">
                                    Permitir descarga del archivo PDF por parte de los alumnos
                                </label>
                            </div>
                            <div class="form-text">
                                Si esta opción está desactivada, los alumnos solo podrán visualizar el libro en línea.
                            </div>
                        </div>
                    </div>

                    <!-- Añade esta sección en la vista edit.blade.php del admin -->
                    <div class="alert alert-info mt-3">
                        <h6 class="alert-heading">
                            <i class="fas fa-user-graduate"></i> Información del Docente
                        </h6>
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Nombre:</strong> {{ $libro->usuario->nombre }}
                            </div>
                            <div class="col-md-6">
                                <strong>Email:</strong> {{ $libro->usuario->email }}
                            </div>
                            <div class="col-md-6">
                                <strong>Subido el:</strong> {{ $libro->created_at->format('d/m/Y H:i') }}
                            </div>
                            <div class="col-md-6">
                                <strong>Última modificación:</strong> {{ $libro->updated_at->format('d/m/Y H:i') }}
                            </div>
                        </div>
                    </div>

                    <!-- Estadísticas -->
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="alert alert-info">
                                <h6 class="alert-heading">
                                    <i class="fas fa-chart-bar"></i> Estadísticas del Libro
                                </h6>
                                <div class="row text-center">
                                    <div class="col-md-4">
                                        <strong>{{ $libro->veces_descargado }}</strong>
                                        <br>
                                        <small>Descargas</small>
                                    </div>
                                    <div class="col-md-4">
                                        <strong>{{ $libro->veces_visto }}</strong>
                                        <br>
                                        <small>Visualizaciones</small>
                                    </div>
                                    <div class="col-md-4">
                                        <strong>{{ $libro->created_at->diffForHumans() }}</strong>
                                        <br>
                                        <small>En la biblioteca</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Botones de Acción -->
                    <div class="d-flex gap-2 justify-content-end mt-4">
                        <a href="{{ route('docente.libros.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-save"></i> Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>