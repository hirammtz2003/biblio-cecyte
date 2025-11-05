<?php

namespace App\Http\Controllers;

use App\Models\Libro;
use Illuminate\Http\Request;

class BusquedaController extends Controller
{
    public function index(Request $request)
    {
        $busqueda = $request->get('busqueda', '');
        $carrera = $request->get('carrera', '');
        $semestre = $request->get('semestre', '');
        
        return view('dashboard', compact('busqueda', 'carrera', 'semestre'));
    }

    public function buscar(Request $request)
    {
        $busqueda = $request->get('busqueda', '');
        $carrera = $request->get('carrera', '');
        $semestre = $request->get('semestre', '');
        
        $query = Libro::query()->where('activo', true);
        
        // Búsqueda en múltiples campos
        if (!empty($busqueda)) {
            $query->where(function($q) use ($busqueda) {
                $q->where('titulo', 'LIKE', "%{$busqueda}%")
                  ->orWhere('autor', 'LIKE', "%{$busqueda}%")
                  ->orWhere('descripcion', 'LIKE', "%{$busqueda}%")
                  ->orWhere('isbn', 'LIKE', "%{$busqueda}%")
                  ->orWhere('materia', 'LIKE', "%{$busqueda}%");
            });
        }
        
        // Filtros
        if (!empty($carrera)) {
            $query->where('carrera', $carrera);
        }
        
        if (!empty($semestre)) {
            $query->where('semestre', $semestre);
        }
        
        $libros = $query->paginate(25);
        
        return view('busqueda.resultados', compact('libros', 'busqueda', 'carrera', 'semestre'));
    }
}