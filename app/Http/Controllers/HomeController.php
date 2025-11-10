<?php

namespace App\Http\Controllers;

use App\Models\Libro;
use App\Models\Usuario;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function welcome()
    {
        $estadisticas = [
            'totalLibros' => Libro::where('activo', true)->count(),
            'totalUsuarios' => Usuario::count(),
            'totalDescargas' => Libro::sum('veces_descargado'),
            'totalCarreras' => 4 // Fijo por ahora
        ];

        return view('welcome', compact('estadisticas'));
    }

    public function getEstadisticas()
    {
        $estadisticas = [
            'totalLibros' => Libro::where('activo', true)->count(),
            'totalUsuarios' => Usuario::count(),
            'totalDescargas' => Libro::sum('veces_descargado'),
            'totalCarreras' => 4
        ];

        return response()->json($estadisticas);
    }
}