<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LibroController;
use App\Http\Controllers\BusquedaController;
//use App\Http\Controllers\ThumbnailController;
use App\Http\Controllers\LibroViewController;
use App\Http\Controllers\FavoritoController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Rutas de autenticación
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Rutas de perfil
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    
    // Ruta protegida del dashboard
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

// Rutas de administración
Route::middleware('auth')->prefix('admin')->group(function () {
    Route::get('/usuarios', [AdminController::class, 'index'])->name('admin.usuarios.index');
    Route::put('/usuarios/{id}', [AdminController::class, 'updateUsuario'])->name('admin.usuarios.update');
    Route::delete('/usuarios/{id}', [AdminController::class, 'destroyUsuario'])->name('admin.usuarios.destroy');
    Route::get('/estadisticas', [AdminController::class, 'getEstadisticas'])->name('admin.estadisticas');
});

// Rutas para docentes (gestión de libros)
Route::middleware('auth')->prefix('docente')->group(function () {
    Route::get('/libros', [LibroController::class, 'index'])->name('docente.libros.index');
    Route::get('/libros/create', [LibroController::class, 'create'])->name('docente.libros.create');
    Route::post('/libros', [LibroController::class, 'store'])->name('docente.libros.store');
    Route::get('/libros/{id}/edit', [LibroController::class, 'edit'])->name('docente.libros.edit');
    Route::put('/libros/{id}', [LibroController::class, 'update'])->name('docente.libros.update');
    Route::delete('/libros/{id}', [LibroController::class, 'destroy'])->name('docente.libros.destroy');
    Route::get('/libros/{id}/download', [LibroController::class, 'download'])->name('docente.libros.download');
});

// Rutas de búsqueda (disponible para todos los usuarios autenticados)
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [BusquedaController::class, 'index'])->name('dashboard');
    Route::get('/buscar', [BusquedaController::class, 'buscar'])->name('busqueda.buscar');
});

// Rutas para thumbnails
/*Route::get('/thumbnail/generar/{libro}', [ThumbnailController::class, 'generarThumbnail'])->name('thumbnail.generar');
Route::get('/thumbnail/{libro}', [ThumbnailController::class, 'obtenerThumbnail'])->name('thumbnail.obtener');
*/
// Rutas para visualizar libros
Route::middleware('auth')->group(function () {
    Route::get('/libro/{libro}', [LibroViewController::class, 'ver'])->name('libro.ver');
    Route::get('/libro/{libro}/descargar', [LibroViewController::class, 'descargar'])->name('libro.descargar');
    Route::get('/libro/{libro}/pdf', [LibroViewController::class, 'verPdf'])->name('libro.pdf');
});

// Rutas para favoritos (disponible para todos los usuarios autenticados)
Route::middleware('auth')->group(function () {
    Route::post('/favoritos/toggle/{libro}', [FavoritoController::class, 'toggleFavorito'])->name('favoritos.toggle');
    Route::get('/favoritos', [FavoritoController::class, 'index'])->name('favoritos.index');
    Route::delete('/favoritos/remove/{libro}', [FavoritoController::class, 'removeFavorito'])->name('favoritos.remove');
});

// Ruta principal
Route::get('/', [HomeController::class, 'welcome'])->name('home');

// API para estadísticas
Route::get('/estadisticas', [HomeController::class, 'getEstadisticas'])->name('estadisticas');