<?php

namespace App\Http\Controllers;

use App\Models\Libro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoritoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function toggleFavorito(Request $request, $libroId)
    {
        \Log::info('toggleFavorito llamado', [
            'libro_id' => $libroId,
            'usuario_id' => Auth::id(),
            'ajax' => $request->ajax(),
            'wants_json' => $request->wantsJson(),
            'accepts_json' => $request->acceptsJson()
        ]);

        try {
            $libro = Libro::findOrFail($libroId);
            $usuario = Auth::user();

            if ($usuario->tieneFavorito($libroId)) {
                // Quitar de favoritos
                $usuario->librosFavoritos()->detach($libroId);
                $esFavorito = false;
                $mensaje = 'Libro removido de favoritos';
            } else {
                // Agregar a favoritos
                $usuario->librosFavoritos()->attach($libroId);
                $esFavorito = true;
                $mensaje = 'Libro agregado a favoritos';
            }

            // Siempre devolver JSON para las peticiones AJAX
            return response()->json([
                'success' => true,
                'esFavorito' => $esFavorito,
                'mensaje' => $mensaje,
                'contadorFavoritos' => $libro->getContadorFavoritos()
            ]);

        } catch (\Exception $e) {
            \Log::error('Error en toggleFavorito: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'mensaje' => 'Error al actualizar favoritos: ' . $e->getMessage()
            ], 500);
        }
    }

    public function index(Request $request)
    {
        $orden = $request->get('orden', 'fecha_desc');
        $busqueda = $request->get('busqueda', '');

        $query = Auth::user()->librosFavoritos()->where('activo', true);

        // Aplicar búsqueda
        if (!empty($busqueda)) {
            $query->buscar($busqueda);
        }

        // Aplicar ordenamiento
        switch ($orden) {
            case 'titulo_asc':
                $query->orderBy('titulo');
                break;
            case 'titulo_desc':
                $query->orderBy('titulo', 'desc');
                break;
            case 'fecha_agregado_desc':
                $query->orderBy('libro_favorito.created_at', 'desc');
                break;
            case 'fecha_agregado_asc':
                $query->orderBy('libro_favorito.created_at', 'asc');
                break;
            case 'fecha_desc':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        $libros = $query->paginate(10);
        $totalFavoritos = Auth::user()->librosFavoritos()->count();

        return view('favoritos.index', compact(
            'libros',
            'totalFavoritos',
            'orden',
            'busqueda'
        ));
    }

    public function removeFavorito($libroId)
    {
        $usuario = Auth::user();
        $usuario->librosFavoritos()->detach($libroId);

        return back()->with('success', 'Libro removido de favoritos');
    }
}