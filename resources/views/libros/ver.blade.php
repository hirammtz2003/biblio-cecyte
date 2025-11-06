<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $libro->titulo }} - Biblioteca CECyTE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .pdf-container {
            height: 70vh;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            overflow: hidden;
        }
        .metadata-table th {
            background-color: #f8f9fa;
            width: 30%;
        }
        .breadcrumb {
            background-color: transparent;
            padding: 0;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="{{ route('dashboard') }}">Biblioteca CECyTE</a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="{{ route('dashboard') }}">Inicio</a>
                <a class="nav-link" href="{{ route('profile.show') }}">Mi Perfil</a>
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
        <!-- Migas de pan -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Inicio</a></li>
                <li class="breadcrumb-item active">{{ Str::limit($libro->titulo, 50) }}</li>
            </ol>
        </nav>

        <div class="row">
            <!-- Visor PDF -->
            <div class="col-12 mb-4">
                <div class="card">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-book"></i> {{ $libro->titulo }}
                        </h5>
                        <div>
                            @if($libro->descargable && $archivoExiste)
                                <a href="{{ route('libro.descargar', $libro->id) }}" class="btn btn-light btn-sm">
                                    <i class="fas fa-download"></i> Descargar PDF
                                </a>
                            @elseif($libro->descargable)
                                <button class="btn btn-light btn-sm" disabled title="Archivo no disponible">
                                    <i class="fas fa-download"></i> Descargar PDF
                                </button>
                            @else
                                <span class="badge bg-warning text-dark">
                                    <i class="fas fa-eye"></i> Solo lectura
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="card-body p-0">
                        @if($archivoExiste)
                            <div class="pdf-container">
                                <iframe src="{{ route('libro.pdf', $libro->id) }}"
                                        width="100%" 
                                        height="100%"
                                        frameborder="0"
                                        style="border: none;">
                                    <p>Tu navegador no soporta iframes. <a href="{{ route('libro.pdf', $libro->id) }}">Ver PDF</a></p>
                                </iframe>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                                <h4 class="text-warning">Archivo no disponible</h4>
                                <p class="text-muted">El archivo PDF no se encuentra en el servidor.</p>
                                <p class="text-muted"><small>Ruta esperada: {{ $libro->ruta_archivo }}</small></p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Metadatos -->
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-info-circle"></i> Información del Recurso
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered metadata-table">
                                <tbody>
                                    <tr>
                                        <th>Título</th>
                                        <td>{{ $libro->titulo }}</td>
                                    </tr>
                                    <tr>
                                        <th>Autor</th>
                                        <td>{{ $libro->autor }}</td>
                                    </tr>
                                    @if($libro->anio_publicacion)
                                    <tr>
                                        <th>Año de publicación</th>
                                        <td>{{ $libro->anio_publicacion }}</td>
                                    </tr>
                                    @endif
                                    @if($libro->isbn)
                                    <tr>
                                        <th>ISBN</th>
                                        <td>{{ $libro->isbn }}</td>
                                    </tr>
                                    @endif
                                    @if($libro->materia)
                                    <tr>
                                        <th>Materia</th>
                                        <td>{{ $libro->materia }}</td>
                                    </tr>
                                    @endif
                                    @if($libro->carrera)
                                    <tr>
                                        <th>Carrera</th>
                                        <td>{{ $libro->carrera }}</td>
                                    </tr>
                                    @endif
                                    @if($libro->semestre)
                                    <tr>
                                        <th>Semestre</th>
                                        <td>{{ $libro->semestre }}</td>
                                    </tr>
                                    @endif
                                    @if($libro->descripcion)
                                    <tr>
                                        <th>Descripción</th>
                                        <td>{{ $libro->descripcion }}</td>
                                    </tr>
                                    @endif
                                    <tr>
                                        <th>Disponible para descarga</th>
                                        <td>
                                            @if($libro->descargable)
                                                <span class="badge bg-success">Sí</span>
                                            @else
                                                <span class="badge bg-warning text-dark">Solo lectura</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Estadísticas</th>
                                        <td>
                                            <small class="text-muted">
                                                <i class="fas fa-eye"></i> {{ $libro->veces_visto }} vistas |
                                                <i class="fas fa-download"></i> {{ $libro->veces_descargado }} descargas
                                            </small>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>