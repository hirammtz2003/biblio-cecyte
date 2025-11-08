<!-- Mostrar criterios de búsqueda -->
@if(!empty($busqueda) || !empty($carrera) || !empty($semestre))
<div class="alert alert-light mb-4">
    <h6>Resultados de búsqueda:</h6>
    @if(!empty($busqueda))
        <span class="badge bg-primary me-2">Término: "{{ $busqueda }}"</span>
    @endif
    @if(!empty($carrera))
        <span class="badge bg-success me-2">Carrera: {{ $carrera }}</span>
    @endif
    @if(!empty($semestre))
        <span class="badge bg-warning me-2">Semestre: {{ $semestre }}</span>
    @endif
    <span class="badge bg-secondary">{{ $libros->total() }} resultado(s) encontrado(s)</span>
</div>
@endif

<!-- Grid de libros 5x5 -->
@if($libros->count() > 0)
    <div class="row row-cols-2 row-cols-md-3 row-cols-lg-5 g-4">
        @foreach($libros as $libro)
            <div class="col">
                <div class="card book-card">
                    <div class="book-cover text-center p-3 bg-light rounded">
                        <div class="d-flex flex-column justify-content-center align-items-center h-100">
                            <i class="fas fa-file-pdf fa-3x text-danger mb-2"></i>
                            <small class="text-muted">{{ $libro->getTamanioFormateado() }}</small>
                            <!--<small class="text-muted">{{ $libro->paginas ?? 'N/A' }} páginas</small>-->
                        </div>
                    </div>
                    <div class="card-body text-center">
                        <h6 class="book-title">{{ $libro->titulo }}</h6>
                        <small class="text-muted">{{ $libro->autor }}</small>
                    </div>
                    <div class="card-footer bg-transparent">
                        <a href="{{ route('libro.ver', $libro->id) }}" class="btn btn-sm btn-outline-primary w-100">
                            <i class="fas fa-eye"></i> Ver
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Paginación -->
    <div class="d-flex justify-content-center mt-4">
        {{ $libros->appends(request()->query())->links() }}
    </div>
@else
    <div class="text-center py-5">
        <i class="fas fa-search fa-3x text-muted mb-3"></i>
        <h4 class="text-muted">No se encontraron resultados</h4>
        <p class="text-muted">Intenta con otros términos de búsqueda o filtros.</p>
        <button class="btn btn-primary" onclick="limpiarBusqueda()">
            <i class="fas fa-times"></i> Limpiar búsqueda
        </button>
    </div>
@endif