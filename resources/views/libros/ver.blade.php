<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $libro->titulo }} - Biblioteca CECyTE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- PDF.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.4.120/pdf.min.js"></script>
    <style>
        .pdf-container {
            height: 70vh;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            overflow: hidden;
            background-color: #f8f9fa;
        }
        #pdf-canvas {
            border: 1px solid #dee2e6;
            max-width: 100%;
            height: auto;
        }
        .pdf-controls {
            background-color: #f8f9fa;
            padding: 10px;
            border-bottom: 1px solid #dee2e6;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
        }
        #page-navigation {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .navbar-brand {
            font-weight: bold;
            font-size: 1.5rem;
        }
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
            <a class="navbar-brand" href="{{ route('dashboard') }}">
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
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Inicio</a></li>
                <li class="breadcrumb-item active">{{ Str::limit($libro->titulo, 50) }}</li>
            </ol>
        </nav>

        <div class="row">
            <div class="col-12 mb-4">
                <div class="card">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-book"></i> {{ $libro->titulo }}
                        </h5>
                        <div>
                            <!-- Botones de favoritos y descarga -->
                            <button id="favoritoBtn" 
                                    class="btn {{ Auth::user()->tieneFavorito($libro->id) ? 'btn-warning' : 'btn-light' }} btn-sm me-2"
                                    data-libro-id="{{ $libro->id }}">
                                <i class="fas {{ Auth::user()->tieneFavorito($libro->id) ? 'fa-star' : 'fa-star' }}"></i>
                                <span id="favoritoText">
                                    {{ Auth::user()->tieneFavorito($libro->id) ? 'En favoritos' : 'Agregar a favoritos' }}
                                </span>
                            </button>

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
                                <!-- Controles básicos -->
                                <div class="pdf-controls">
                                    <div id="page-navigation">
                                        <button id="prev-page" class="btn btn-sm btn-outline-secondary">
                                            <i class="fas fa-chevron-left"></i> Anterior
                                        </button>
                                        <span class="mx-2">Página: 
                                            <span id="page-num">1</span> / 
                                            <span id="page-count">0</span>
                                        </span>
                                        <button id="next-page" class="btn btn-sm btn-outline-secondary">
                                            Siguiente <i class="fas fa-chevron-right"></i>
                                        </button>
                                    </div>
                                    <div>
                                        <select id="zoom-select" class="form-select form-select-sm">
                                            <option value="0.5">50%</option>
                                            <option value="0.75">75%</option>
                                            <option value="1" selected>100%</option>
                                            <option value="1.25">125%</option>
                                            <option value="1.5">150%</option>
                                            <option value="2">200%</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <!-- Visor PDF -->
                                <div id="pdf-viewer-container" class="d-flex justify-content-center p-3">
                                    <canvas id="pdf-canvas"></canvas>
                                </div>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                                <h4 class="text-warning">Archivo no disponible</h4>
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
    <script>
    // Configurar PDF.js
    pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.4.120/pdf.worker.min.js';

    document.addEventListener('DOMContentLoaded', function() {
        let pdfDoc = null;
        let pageNum = 1;
        let scale = 1;
        const canvas = document.getElementById('pdf-canvas');
        const ctx = canvas.getContext('2d');

        // Cargar el PDF desde la nueva ruta stream
        const loadingTask = pdfjsLib.getDocument('{{ route("libro.stream", $libro->id) }}');
        
        loadingTask.promise.then(function(pdf) {
            pdfDoc = pdf;
            document.getElementById('page-count').textContent = pdf.numPages;
            renderPage(pageNum);
        }).catch(function(error) {
            console.error('Error al cargar PDF:', error);
            document.getElementById('pdf-viewer-container').innerHTML = `
                <div class="text-center py-5">
                    <i class="fas fa-exclamation-triangle fa-2x text-danger mb-3"></i>
                    <p>Error al cargar el PDF</p>
                </div>
            `;
        });

        function renderPage(num) {
            pdfDoc.getPage(num).then(function(page) {
                const viewport = page.getViewport({ scale: scale });
                canvas.height = viewport.height;
                canvas.width = viewport.width;
                
                const renderContext = {
                    canvasContext: ctx,
                    viewport: viewport
                };
                
                page.render(renderContext);
                document.getElementById('page-num').textContent = num;
            });
        }

        // Navegación
        document.getElementById('prev-page').addEventListener('click', function() {
            if (pageNum <= 1) return;
            pageNum--;
            renderPage(pageNum);
        });
        
        document.getElementById('next-page').addEventListener('click', function() {
            if (pageNum >= pdfDoc.numPages) return;
            pageNum++;
            renderPage(pageNum);
        });

        // Zoom
        document.getElementById('zoom-select').addEventListener('change', function() {
            scale = parseFloat(this.value);
            renderPage(pageNum);
        });

        // Prevenir acciones no deseadas
        canvas.addEventListener('contextmenu', function(e) {
            e.preventDefault();
            return false;
        });
    });
    </script>
</body>
</html>