<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $libro->titulo }} - Biblioteca CECyTE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
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
        <!--  -->
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
                            <!-- Botón de favoritos -->
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
    <script>
    document.addEventListener('DOMContentLoaded', function() {
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
        
        function mostrarMensaje(mensaje, tipo) {
            // Remover mensajes existentes
            const mensajesExistentes = document.querySelectorAll('.alert-temporal');
            mensajesExistentes.forEach(msg => msg.remove());
            
            const alert = document.createElement('div');
            alert.className = `alert alert-${tipo} alert-temporal alert-dismissible fade show position-fixed`;
            alert.style.top = '20px';
            alert.style.right = '20px';
            alert.style.zIndex = '1050';
            alert.style.minWidth = '300px';
            alert.innerHTML = `
                <i class="fas ${tipo === 'success' ? 'fa-check-circle' : 'fa-info-circle'} me-2"></i>
                ${mensaje}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            document.body.appendChild(alert);
            
            // Auto-remover después de 4 segundos
            setTimeout(() => {
                if (alert.parentNode) {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }
            }, 4000);
        }
    });
    </script>
</body>
</html>