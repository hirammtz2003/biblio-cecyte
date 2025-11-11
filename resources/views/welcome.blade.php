<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Biblioteca Virtual - CECyTE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="{{ asset('css/estilos.css') }}" rel="stylesheet">
    <style>
        .hero-section {
            background: linear-gradient(135deg, #4e0d0dff 0%, #a01508 100%);
            color: white;
            padding: 6rem 0;
        }
        .feature-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s;
            height: 100%;
        }
        .feature-card:hover {
            transform: translateY(-5px);
        }
        .nav-brand {
            font-weight: bold;
            font-size: 1.5rem;
        }
        .btn-cecyte {
            background: linear-gradient(45deg, #667eea, #764ba2);
            border: none;
            color: white;
            padding: 12px 30px;
            border-radius: 25px;
            font-weight: 600;
        }
        .btn-cecyte:hover {
            background: linear-gradient(45deg, #5a6fd8, #6a4190);
            color: white;
        }
        .stats-section {
            background-color: #f8f9fa;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand nav-brand" href="{{ url('/') }}">
                <i class="fas fa-book"></i> Biblioteca CECyTE
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="#features">Características</a>
                <a class="nav-link" href="{{ route('login') }}">Iniciar Sesión</a>
                <a class="nav-link" href="{{ route('register') }}">Registrarse</a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section text-center">
        <div class="container">
            <h1 class="display-4 fw-bold mb-4">
                Biblioteca Virtual CECyTEZ Río Grande   
            </h1>
            <p class="lead mb-4">
                Accede a una amplia colección de material bibliográfico y didáctico 
                para apoyar tu formación académica
            </p>
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="input-group input-group-lg">
                        <input type="text" class="form-control" placeholder="Buscar libros, autores, materias..." disabled>
                        <button class="btn btn-cecyte" type="button" disabled>
                            <i class="fas fa-search"></i> Buscar
                        </button>
                    </div>
                    <small class="text-light mt-2 d-block">
                        Inicia sesión para acceder a la búsqueda avanzada
                    </small>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-5">
        <div class="container">
            <div class="row text-center mb-5">
                <div class="col-12">
                    <h2 class="fw-bold">¿Qué ofrece nuestra biblioteca?</h2>
                    <p class="lead text-muted">Características principales de nuestra plataforma</p>
                </div>
            </div>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card feature-card">
                        <div class="card-body text-center p-4">
                            <div class="feature-icon mb-3">
                                <i class="fas fa-search fa-3x text-primary"></i>
                            </div>
                            <h5 class="card-title">Búsqueda Avanzada</h5>
                            <p class="card-text text-muted">
                                Encuentra libros por título, autor, materia, carrera o semestre 
                                con nuestro sistema de búsqueda inteligente.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card feature-card">
                        <div class="card-body text-center p-4">
                            <div class="feature-icon mb-3">
                                <i class="fas fa-star fa-3x text-warning"></i>
                            </div>
                            <h5 class="card-title">Favoritos</h5>
                            <p class="card-text text-muted">
                                Marca tus libros favoritos para tener acceso rápido 
                                y crear tu propia biblioteca personalizada.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card feature-card">
                        <div class="card-body text-center p-4">
                            <div class="feature-icon mb-3">
                                <i class="fas fa-download fa-3x text-success"></i>
                            </div>
                            <h5 class="card-title">Descargas</h5>
                            <p class="card-text text-muted">
                                Descarga los materiales disponibles para estudiar 
                                sin conexión cuando lo necesites.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats-section py-5">
        <div class="container">
            <div class="row text-center">
                <div class="col-md-3 mb-4">
                    <div class="stat-item">
                        <h3 class="fw-bold text-primary" id="totalLibros">0</h3>
                        <p class="text-muted">Libros Disponibles</p>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="stat-item">
                        <h3 class="fw-bold text-success" id="totalUsuarios">0</h3>
                        <p class="text-muted">Usuarios Registrados</p>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="stat-item">
                        <h3 class="fw-bold text-warning" id="totalDescargas">0</h3>
                        <p class="text-muted">Descargas Totales</p>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="stat-item">
                        <h3 class="fw-bold text-info" id="totalCarreras">4</h3>
                        <p class="text-muted">Carreras</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action -->
    <section class="py-5 bg-dark text-white">
        <div class="container text-center">
            <h2 class="fw-bold mb-4">¿Listo para comenzar?</h2>
            <p class="lead mb-4">
                Únete a nuestra comunidad educativa y accede a todos los recursos disponibles
            </p>
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="d-grid gap-3 d-md-block">
                        <a href="{{ route('register') }}" class="btn btn-cecyte btn-lg me-md-3 mb-2">
                            <i class="fas fa-user-plus"></i> Crear Cuenta
                        </a>
                        <a href="{{ route('login') }}" class="btn btn-outline-light btn-lg mb-2">
                            <i class="fas fa-sign-in-alt"></i> Iniciar Sesión
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-light py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5><i class="fas fa-book"></i> Biblioteca CECyTE</h5>
                    <p class="mb-0">Sistema de gestión bibliotecaria para la comunidad educativa</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="mb-0">&copy; 2024 Biblioteca CECyTE. Todos los derechos reservados.</p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Animación simple para los contadores
        function animateCounter(element, target) {
            let current = 0;
            const increment = target / 100;
            const timer = setInterval(() => {
                current += increment;
                if (current >= target) {
                    current = target;
                    clearInterval(timer);
                }
                element.textContent = Math.floor(current);
            }, 20);
        }

        // Ejemplo de estadísticas
        document.addEventListener('DOMContentLoaded', function() {
            // Cargar estadísticas reales
            fetch('/estadisticas')
                .then(response => response.json())
                .then(estadisticas => {
                    animateCounter(document.getElementById('totalLibros'), estadisticas.totalLibros);
                    animateCounter(document.getElementById('totalUsuarios'), estadisticas.totalUsuarios);
                    animateCounter(document.getElementById('totalDescargas'), estadisticas.totalDescargas);
                })
                .catch(error => {
                    console.error('Error cargando estadísticas:', error);
                    // Usar valores por defecto en caso de error
                    animateCounter(document.getElementById('totalLibros'), 150);
                    animateCounter(document.getElementById('totalUsuarios'), 85);
                    animateCounter(document.getElementById('totalDescargas'), 420);
                });
        });
    </script>
</body>
</html>