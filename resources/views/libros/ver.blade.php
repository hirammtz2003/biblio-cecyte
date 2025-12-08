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
        :root {
            --thumbnail-width: 120px;
            --thumbnail-height: 160px;
            --sidebar-width: 150px;
        }   
        .pdf-container {
            height: 70vh;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            overflow: hidden;
            background-color: #f8f9fa;
            display: flex;
            flex-direction: row;
        }        
        /* Sidebar de miniaturas */
        .thumbnails-sidebar {
            width: var(--sidebar-width);
            background-color: #2c3e50;
            border-right: 1px solid #dee2e6;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            padding: 10px 0;
        }        
        .thumbnail-container {
            padding: 8px;
            cursor: pointer;
            border-bottom: 1px solid #34495e;
            transition: all 0.2s;
        }        
        .thumbnail-container:hover {
            background-color: #34495e;
        }
        
        .thumbnail-container.active {
            background-color: #3498db;
        }        
        .thumbnail-canvas {
            width: 100%;
            height: var(--thumbnail-height);
            background-color: white;
            border: 2px solid transparent;
            border-radius: 4px;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }        
        .thumbnail-container.active .thumbnail-canvas {
            border-color: #3498db;
            box-shadow: 0 0 0 2px rgba(52, 152, 219, 0.3);
        }        
        .thumbnail-page-number {
            text-align: center;
            color: #ecf0f1;
            font-size: 0.8rem;
            margin-top: 5px;
            font-weight: bold;
        }        
        .thumbnail-container.active .thumbnail-page-number {
            color: #3498db;
        }        
        .thumbnail-loading {
            color: #7f8c8d;
            font-size: 0.7rem;
        }        
        /* Área principal del visor */
        .pdf-main-area {
            flex: 1;
            display: flex;
            flex-direction: column;
        }        
        .pdf-controls {
            background-color: #f8f9fa;
            padding: 12px;
            border-bottom: 1px solid #dee2e6;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
        }        
        .control-group {
            display: flex;
            align-items: center;
            gap: 10px;
        }        
        .page-info {
            font-weight: bold;
            color: #495057;
            min-width: 120px;
            text-align: center;
        }        
        .page-input-group {
            display: flex;
            align-items: center;
            gap: 5px;
        }        
        .page-input {
            width: 70px;
            text-align: center;
            padding: 4px 8px;
            border: 1px solid #dee2e6;
            border-radius: 4px;
        }        
        .zoom-controls {
            display: flex;
            align-items: center;
            gap: 5px;
        }        
        .btn-control {
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            padding: 6px 12px;
            cursor: pointer;
            transition: all 0.2s;
            font-size: 0.875rem;
            display: flex;
            align-items: center;
            gap: 5px;
        }        
        .btn-control:hover:not(:disabled) {
            background: #e9ecef;
            border-color: #adb5bd;
        }        
        .btn-control:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }        
        /* Botón de toggle para miniaturas - visible en desktop también */
        .btn-thumbnails-toggle.active {
            background-color: #3498db !important;
            color: white !important;
        }

        /* Ocultar sidebar por defecto en desktop */
        .thumbnails-sidebar {
            width: var(--sidebar-width);
            background-color: #2c3e50;
            border-right: 1px solid #dee2e6;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            padding: 10px 0;
            transition: width 0.3s ease, transform 0.3s ease;
        }

        /* Estado cerrado del sidebar */
        .thumbnails-sidebar.closed {
            width: 0;
            overflow: hidden;
            padding: 0;
            border-right: none;
        }

        /* Para móviles - comportamiento diferente */
        @media (max-width: 992px) {
            .thumbnails-sidebar {
                position: fixed;
                left: 0;
                top: 0;
                bottom: 0;
                z-index: 1000;
                transform: translateX(-100%);
                transition: transform 0.3s ease;
                box-shadow: 2px 0 10px rgba(0,0,0,0.3);
                width: var(--sidebar-width);
            }
            
            .thumbnails-sidebar.open {
                transform: translateX(0);
            }
            
            .pdf-container {
                position: relative;
            }
        }       
        /* Área del visor principal */
        .pdf-viewer-wrapper {
            flex: 1;
            overflow: auto;
            background-color: #525659;
            position: relative;
        }        
        #pdf-canvas {
            display: block;
            margin: 20px auto;
            box-shadow: 0 4px 8px rgba(0,0,0,0.3);
        }        
        .loading-spinner {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: #fff;
            text-align: center;
            z-index: 10;
        }        
        .pdf-error {
            color: #fff;
            text-align: center;
            padding: 40px;
        }        
        /* Scrollbar personalizado */
        .thumbnails-sidebar::-webkit-scrollbar,
        .pdf-viewer-wrapper::-webkit-scrollbar {
            width: 10px;
        }        
        .thumbnails-sidebar::-webkit-scrollbar-track {
            background: #34495e;
            border-radius: 4px;
        }        
        .thumbnails-sidebar::-webkit-scrollbar-thumb {
            background: #7f8c8d;
            border-radius: 4px;
        }        
        .thumbnails-sidebar::-webkit-scrollbar-thumb:hover {
            background: #95a5a6;
        }        
        .pdf-viewer-wrapper::-webkit-scrollbar-track {
            background: #424242;
            border-radius: 4px;
        }        
        .pdf-viewer-wrapper::-webkit-scrollbar-thumb {
            background: #686868;
            border-radius: 4px;
        }        
        .pdf-viewer-wrapper::-webkit-scrollbar-thumb:hover {
            background: #7a7a7a;
        }        
        /* Responsive */
        @media (max-width: 992px) {
            .thumbnails-sidebar {
                position: absolute;
                left: 0;
                top: 0;
                bottom: 0;
                z-index: 1000;
                transform: translateX(-100%);
                transition: transform 0.3s ease;
                box-shadow: 2px 0 10px rgba(0,0,0,0.3);
            }
            
            .thumbnails-sidebar.open {
                transform: translateX(0);
            }
            
            .pdf-container {
                position: relative;
            }
        }        
        @media (max-width: 768px) {
            .pdf-controls {
                flex-direction: column;
                gap: 15px;
            }
            
            .control-group {
                justify-content: center;
                flex-wrap: wrap;
            }
        }
        /* Navbar */
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
                            <button id="favoritoBtn" 
                                    class="btn {{ Auth::user()->tieneFavorito($libro->id) ? 'btn-warning' : 'btn-light' }} btn-sm me-2"
                                    data-libro-id="{{ $libro->id }}">
                                <i class="fas fa-star"></i>
                                <span id="favoritoText">
                                    {{ Auth::user()->tieneFavorito($libro->id) ? 'En favoritos' : 'Agregar a favoritos' }}
                                </span>
                            </button>

                            @if($libro->descargable && $archivoExiste)
                                <a href="{{ route('libro.descargar', $libro->id) }}" class="btn btn-light btn-sm">
                                    <i class="fas fa-download"></i> Descargar PDF
                                </a>
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
                                <!-- Sidebar de miniaturas -->
                                <div class="thumbnails-sidebar" id="thumbnails-sidebar">
                                    <!-- Las miniaturas se generarán dinámicamente -->
                                    <div class="text-center text-light p-3">
                                        <div class="spinner-border spinner-border-sm" role="status">
                                            <span class="visually-hidden">Cargando miniaturas...</span>
                                        </div>
                                        <div class="mt-2">Cargando miniaturas...</div>
                                    </div>
                                </div>
                                
                                <!-- Área principal -->
                                <div class="pdf-main-area">
                                    <!-- Controles -->
                                    <div class="pdf-controls">
                                        <div class="control-group">
                                            <button id="toggle-thumbnails" class="btn-control btn-thumbnails-toggle" title="Mostrar/Ocultar miniaturas">
                                                <i class="fas fa-th"></i>
                                            </button>
                                            <button id="first-page" class="btn-control" title="Primera página">
                                                <i class="fas fa-fast-backward"></i>
                                            </button>
                                            <button id="prev-page" class="btn-control" title="Página anterior">
                                                <i class="fas fa-chevron-left"></i>
                                            </button>
                                            
                                            <div class="page-input-group">
                                                <input type="number" id="page-input" class="page-input" min="1" value="1">
                                                <span>de <span id="page-count">0</span></span>
                                            </div>
                                            
                                            <button id="go-to-page" class="btn-control" title="Ir a página">
                                                <i class="fas fa-arrow-right"></i>
                                            </button>
                                            
                                            <button id="next-page" class="btn-control" title="Página siguiente">
                                                <i class="fas fa-chevron-right"></i>
                                            </button>
                                            <button id="last-page" class="btn-control" title="Última página">
                                                <i class="fas fa-fast-forward"></i>
                                            </button>
                                        </div>
                                        
                                        <div class="control-group">
                                            <button id="zoom-out" class="btn-control" title="Alejar">
                                                <i class="fas fa-search-minus"></i>
                                            </button>
                                            <span id="zoom-level" class="page-info">100%</span>
                                            <button id="zoom-in" class="btn-control" title="Acercar">
                                                <i class="fas fa-search-plus"></i>
                                            </button>
                                            <button id="fit-width" class="btn-control" title="Ajustar al ancho">
                                                <i class="fas fa-arrows-alt-h"></i>
                                            </button>
                                            <button id="fit-page" class="btn-control" title="Ajustar a la página">
                                                <i class="fas fa-arrows-alt-v"></i>
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <!-- Visor principal -->
                                    <div class="pdf-viewer-wrapper" id="pdf-viewer-wrapper">
                                        <div class="loading-spinner" id="loading-spinner">
                                            <div class="spinner-border text-light" role="status">
                                                <span class="visually-hidden">Cargando...</span>
                                            </div>
                                            <div class="mt-2 text-light">Cargando página...</div>
                                        </div>
                                        <canvas id="pdf-canvas"></canvas>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                                <h4 class="text-warning">Archivo no disponible</h4>
                                <p class="text-muted">El archivo PDF no se encuentra en el servidor.</p>
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
        let currentPage = 1;
        let currentScale = 1.0;
        let isRendering = false;
        let pageNumPending = null;
        let thumbnails = [];
        
        const canvas = document.getElementById('pdf-canvas');
        const ctx = canvas.getContext('2d');
        const loadingSpinner = document.getElementById('loading-spinner');
        const viewerWrapper = document.getElementById('pdf-viewer-wrapper');
        const thumbnailsSidebar = document.getElementById('thumbnails-sidebar');
        const pageInput = document.getElementById('page-input');

        // Mostrar spinner de carga
        function showLoading() {
            loadingSpinner.style.display = 'block';
        }

        // Ocultar spinner
        function hideLoading() {
            loadingSpinner.style.display = 'none';
        }

        // Actualizar controles
        function updateControls() {
            document.getElementById('zoom-level').textContent = Math.round(currentScale * 100) + '%';
            pageInput.value = currentPage;
            
            document.getElementById('first-page').disabled = (currentPage <= 1);
            document.getElementById('prev-page').disabled = (currentPage <= 1);
            document.getElementById('next-page').disabled = (currentPage >= pdfDoc.numPages);
            document.getElementById('last-page').disabled = (currentPage >= pdfDoc.numPages);
            document.getElementById('zoom-out').disabled = (currentScale <= 0.5);
            document.getElementById('zoom-in').disabled = (currentScale >= 3.0);
            
            // Actualizar miniaturas activas
            updateActiveThumbnail();
        }

        // Renderizar página principal
        function renderPage(num) {
            if (isRendering) {
                pageNumPending = num;
                return;
            }
            
            isRendering = true;
            showLoading();
            
            pdfDoc.getPage(num).then(function(page) {
                const viewport = page.getViewport({ scale: currentScale });
                
                // Ajustar tamaño del canvas
                canvas.height = viewport.height;
                canvas.width = viewport.width;
                
                const renderContext = {
                    canvasContext: ctx,
                    viewport: viewport
                };
                
                const renderTask = page.render(renderContext);
                
                renderTask.promise.then(function() {
                    isRendering = false;
                    hideLoading();
                    currentPage = num;
                    updateControls();
                    
                    if (pageNumPending !== null) {
                        renderPage(pageNumPending);
                        pageNumPending = null;
                    }
                });
            }).catch(function(error) {
                console.error('Error al renderizar página:', error);
                hideLoading();
                isRendering = false;
            });
        }

        function queueRenderPage(num) {
            if (isRendering) {
                pageNumPending = num;
            } else {
                renderPage(num);
            }
        }

        // Generar miniaturas
        function generateThumbnails() {
            thumbnailsSidebar.innerHTML = '';
            thumbnails = [];
            
            for (let i = 1; i <= pdfDoc.numPages; i++) {
                const thumbnailContainer = document.createElement('div');
                thumbnailContainer.className = 'thumbnail-container';
                thumbnailContainer.dataset.page = i;
                
                const thumbnailCanvas = document.createElement('canvas');
                thumbnailCanvas.className = 'thumbnail-canvas';
                thumbnailCanvas.width = 100;
                thumbnailCanvas.height = 130;
                
                const pageNumber = document.createElement('div');
                pageNumber.className = 'thumbnail-page-number';
                pageNumber.textContent = `Pág. ${i}`;
                
                thumbnailContainer.appendChild(thumbnailCanvas);
                thumbnailContainer.appendChild(pageNumber);
                
                // Click en miniatura
                thumbnailContainer.addEventListener('click', function() {
                    const pageNum = parseInt(this.dataset.page);
                    if (pageNum !== currentPage) {
                        currentPage = pageNum;
                        queueRenderPage(currentPage);
                        // Desplazar al visor de miniaturas activo
                        this.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                });
                
                thumbnailsSidebar.appendChild(thumbnailContainer);
                thumbnails.push({
                    container: thumbnailContainer,
                    canvas: thumbnailCanvas,
                    rendered: false
                });
                
                // Renderizar miniatura con baja calidad para mejor rendimiento
                if (i <= 10) { // Renderizar primeras 10 inmediatamente
                    renderThumbnail(i);
                } else if (i <= 30) { // Las siguientes 20 con delay
                    setTimeout(() => renderThumbnail(i), (i - 10) * 100);
                } else { // El resto lazy loading
                    createLazyLoader(i);
                }
            }
        }

        // Renderizar miniatura individual
        function renderThumbnail(pageNum) {
            const thumb = thumbnails[pageNum - 1];
            if (!thumb || thumb.rendered) return;
            
            pdfDoc.getPage(pageNum).then(function(page) {
                const viewport = page.getViewport({ scale: 0.2 }); // Scale pequeño para miniaturas
                const thumbCtx = thumb.canvas.getContext('2d');
                
                thumb.canvas.width = viewport.width;
                thumb.canvas.height = viewport.height;
                
                const renderContext = {
                    canvasContext: thumbCtx,
                    viewport: viewport
                };
                
                page.render(renderContext).promise.then(function() {
                    thumb.rendered = true;
                });
            });
        }

        // Lazy loading para miniaturas
        function createLazyLoader(pageNum) {
            const observer = new IntersectionObserver(function(entries) {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        renderThumbnail(pageNum);
                        observer.unobserve(entry.target);
                    }
                });
            }, { root: thumbnailsSidebar, threshold: 0.1 });
            
            observer.observe(thumbnails[pageNum - 1].container);
        }

        // Actualizar miniatura activa
        function updateActiveThumbnail() {
            thumbnails.forEach((thumb, index) => {
                if (index + 1 === currentPage) {
                    thumb.container.classList.add('active');
                    // Desplazar al visor de miniaturas activo
                    thumb.container.scrollIntoView({ behavior: 'smooth', block: 'center' });
                } else {
                    thumb.container.classList.remove('active');
                }
            });
        }

        // Cargar el PDF
        showLoading();
        const loadingTask = pdfjsLib.getDocument('{{ route("libro.stream", $libro->id) }}');
        
        loadingTask.promise.then(function(pdf) {
            pdfDoc = pdf;
            document.getElementById('page-count').textContent = pdf.numPages;
            generateThumbnails();
            updateControls();
            renderPage(currentPage);
        }).catch(function(error) {
            console.error('Error al cargar PDF:', error);
            viewerWrapper.innerHTML = `
                <div class="pdf-error">
                    <i class="fas fa-exclamation-triangle fa-2x mb-3"></i>
                    <p>Error al cargar el PDF</p>
                    <small>${error.message}</small>
                </div>
            `;
        });

        // Event listeners para navegación
        document.getElementById('first-page').addEventListener('click', function() {
            if (currentPage <= 1) return;
            currentPage = 1;
            queueRenderPage(currentPage);
        });

        document.getElementById('prev-page').addEventListener('click', function() {
            if (currentPage <= 1) return;
            currentPage--;
            queueRenderPage(currentPage);
        });
        
        document.getElementById('next-page').addEventListener('click', function() {
            if (currentPage >= pdfDoc.numPages) return;
            currentPage++;
            queueRenderPage(currentPage);
        });

        document.getElementById('last-page').addEventListener('click', function() {
            if (currentPage >= pdfDoc.numPages) return;
            currentPage = pdfDoc.numPages;
            queueRenderPage(currentPage);
        });

        // Ir a página específica
        document.getElementById('go-to-page').addEventListener('click', function() {
            const pageNum = parseInt(pageInput.value);
            if (!isNaN(pageNum) && pageNum >= 1 && pageNum <= pdfDoc.numPages) {
                if (pageNum !== currentPage) {
                    currentPage = pageNum;
                    queueRenderPage(currentPage);
                }
            } else {
                alert(`Por favor ingresa un número entre 1 y ${pdfDoc.numPages}`);
                pageInput.value = currentPage;
            }
        });

        // Permitir Enter en el input
        pageInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                document.getElementById('go-to-page').click();
            }
        });

        // Event listeners para zoom
        document.getElementById('zoom-out').addEventListener('click', function() {
            if (currentScale <= 0.5) return;
            currentScale = Math.max(0.5, currentScale - 0.25);
            queueRenderPage(currentPage);
        });

        document.getElementById('zoom-in').addEventListener('click', function() {
            if (currentScale >= 3.0) return;
            currentScale = Math.min(3.0, currentScale + 0.25);
            queueRenderPage(currentPage);
        });

        // Ajustes de visualización
        document.getElementById('fit-width').addEventListener('click', function() {
            pdfDoc.getPage(currentPage).then(function(page) {
                const viewport = page.getViewport({ scale: 1 });
                const wrapperWidth = viewerWrapper.clientWidth - 40;
                currentScale = wrapperWidth / viewport.width;
                queueRenderPage(currentPage);
            });
        });

        document.getElementById('fit-page').addEventListener('click', function() {
            pdfDoc.getPage(currentPage).then(function(page) {
                const viewport = page.getViewport({ scale: 1 });
                const wrapperWidth = viewerWrapper.clientWidth - 40;
                const wrapperHeight = viewerWrapper.clientHeight - 40;
                const scaleX = wrapperWidth / viewport.width;
                const scaleY = wrapperHeight / viewport.height;
                currentScale = Math.min(scaleX, scaleY);
                queueRenderPage(currentPage);
            });
        });

        // Toggle sidebar de miniaturas
        document.getElementById('toggle-thumbnails').addEventListener('click', function() {
            const sidebar = document.getElementById('thumbnails-sidebar');
            const isMobile = window.innerWidth <= 992;
            
            if (isMobile) {
                // En móviles: toggle clase 'open'
                sidebar.classList.toggle('open');
            } else {
                // En desktop: toggle clase 'closed'
                sidebar.classList.toggle('closed');
                this.classList.toggle('active');
                
                // Actualizar tooltip
                const isClosed = sidebar.classList.contains('closed');
                this.title = isClosed ? 'Mostrar miniaturas' : 'Ocultar miniaturas';
                this.innerHTML = isClosed ? 
                    '<i class="fas fa-th"></i>' : 
                    '<i class="fas fa-times"></i>';
            }
        });

        // Cerrar sidebar al hacer click fuera (solo móviles)
        document.addEventListener('click', function(event) {
            const sidebar = document.getElementById('thumbnails-sidebar');
            const toggleBtn = document.getElementById('toggle-thumbnails');
            
            if (window.innerWidth <= 992) {
                const isClickInsideSidebar = sidebar.contains(event.target);
                const isClickOnToggle = toggleBtn.contains(event.target) || event.target === toggleBtn;
                
                if (!isClickInsideSidebar && !isClickOnToggle && sidebar.classList.contains('open')) {
                    sidebar.classList.remove('open');
                }
            }
        });

        // Actualizar estado al cambiar tamaño de ventana
        window.addEventListener('resize', function() {
            const sidebar = document.getElementById('thumbnails-sidebar');
            const toggleBtn = document.getElementById('toggle-thumbnails');
            const isMobile = window.innerWidth <= 992;
            
            if (isMobile) {
                // En móviles: asegurar que sidebar esté cerrado por defecto
                sidebar.classList.remove('closed');
                toggleBtn.classList.remove('active');
                toggleBtn.innerHTML = '<i class="fas fa-th"></i>';
                toggleBtn.title = 'Mostrar/Ocultar miniaturas';
                
                // Si estaba open en desktop, cerrarlo para móvil
                if (!sidebar.classList.contains('open')) {
                    sidebar.classList.remove('open');
                }
            } else {
                // En desktop: asegurar que no tenga clase 'open'
                sidebar.classList.remove('open');
            }
        });

        // Prevenir acciones no deseadas
        canvas.addEventListener('contextmenu', function(e) {
            e.preventDefault();
            return false;
        });

        canvas.addEventListener('dragstart', function(e) {
            e.preventDefault();
            return false;
        });

        // Código de favoritos
        const favoritoBtn = document.getElementById('favoritoBtn');
        if (favoritoBtn) {
            favoritoBtn.addEventListener('click', function() {
                const libroId = this.dataset.libroId;
                const btn = this;
                const icon = btn.querySelector('i');
                const text = document.getElementById('favoritoText');
                
                // Mostrar indicador de carga
                btn.disabled = true;
                const originalText = text.textContent;
                text.textContent = 'Procesando...';
                
                fetch(`/favoritos/toggle/${libroId}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({}) // Enviar objeto vacío como body
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Error en la respuesta del servidor: ' + response.status);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        // Actualizar apariencia del botón
                        if (data.esFavorito) {
                            btn.classList.remove('btn-light');
                            btn.classList.add('btn-warning');
                            text.textContent = 'En favoritos';
                        } else {
                            btn.classList.remove('btn-warning');
                            btn.classList.add('btn-light');
                            text.textContent = 'Agregar a favoritos';
                        }
                        
                        // Mostrar mensaje temporal
                        mostrarMensaje(data.mensaje, data.esFavorito ? 'success' : 'info');
                    } else {
                        throw new Error(data.mensaje || 'Error desconocido');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    mostrarMensaje('Error al actualizar favoritos: ' + error.message, 'danger');
                    // Revertir texto del botón
                    text.textContent = originalText;
                })
                .finally(() => {
                    btn.disabled = false;
                });
            });
        }

        // Inicializar estado del sidebar
        function initializeThumbnailsSidebar() {
            const sidebar = document.getElementById('thumbnails-sidebar');
            const toggleBtn = document.getElementById('toggle-thumbnails');
            const isMobile = window.innerWidth <= 992;
            
            if (isMobile) {
                // En móviles: sidebar oculto por defecto
                sidebar.classList.remove('closed');
                sidebar.classList.remove('open');
                toggleBtn.innerHTML = '<i class="fas fa-th"></i>';
                toggleBtn.title = 'Mostrar miniaturas';
            } else {
                // En desktop: sidebar visible por defecto
                sidebar.classList.remove('closed');
                sidebar.classList.remove('open');
                toggleBtn.innerHTML = '<i class="fas fa-times"></i>';
                toggleBtn.title = 'Ocultar miniaturas';
                toggleBtn.classList.add('active');
            }
        }

        // Llamar a la inicialización cuando el PDF esté listo
        loadingTask.promise.then(function(pdf) {
            pdfDoc = pdf;
            document.getElementById('page-count').textContent = pdf.numPages;
            generateThumbnails();
            updateControls();
            renderPage(currentPage);
            initializeThumbnailsSidebar(); // ← Añadir esta línea
        }).catch(function(error) {
            // ... manejo de error
        });

        // Ajustar inicialmente al ancho
        setTimeout(() => {
            if (pdfDoc) {
                document.getElementById('fit-width').click();
            }
        }, 1000);
    });
    </script>
</body>