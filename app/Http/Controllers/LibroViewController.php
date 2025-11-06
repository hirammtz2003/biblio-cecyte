<?php

namespace App\Http\Controllers;

use App\Models\Libro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LibroViewController extends Controller
{
    public function ver($id)
    {
        $libro = Libro::with('usuario')->findOrFail($id);
        
        // Verificar si el archivo existe
        $archivoExiste = $this->verificarArchivoExiste($libro->ruta_archivo);
        
        if (!$archivoExiste) {
            \Log::error("Archivo no encontrado para libro {$id}: " . $libro->ruta_archivo);
        }
        
        // Incrementar contador de vistas
        $libro->increment('veces_visto');
        
        return view('libros.ver', compact('libro', 'archivoExiste'));
    }

    public function descargar($id)
    {
        $libro = Libro::findOrFail($id);
        
        if (!$libro->descargable) {
            abort(403, 'Este libro no está disponible para descarga');
        }
        
        // Verificar que el archivo existe
        $rutaCompleta = $this->obtenerRutaCompleta($libro->ruta_archivo);
        
        if (!$rutaCompleta || !file_exists($rutaCompleta)) {
            abort(404, "Archivo no encontrado: {$libro->ruta_archivo}");
        }
        
        // Incrementar contador de descargas
        $libro->increment('veces_descargado');
        
        return response()->download($rutaCompleta, $libro->nombre_archivo);
    }

    public function verPdf($id)
    {
        $libro = Libro::findOrFail($id);
        
        // Verificar que el archivo existe
        $rutaCompleta = $this->obtenerRutaCompleta($libro->ruta_archivo);
        
        if (!$rutaCompleta || !file_exists($rutaCompleta)) {
            \Log::error("Archivo PDF no encontrado: " . $libro->ruta_archivo);
            abort(404, "El archivo PDF no se encuentra disponible en este momento.");
        }
        
        return response()->file($rutaCompleta);
    }

    private function obtenerRutaCompleta($rutaArchivo)
    {
        // Probar diferentes ubicaciones comunes
        $posiblesRutas = [
            storage_path('app/' . $rutaArchivo),        // storage/app/...
            storage_path('app/public/' . $rutaArchivo), // storage/app/public/...
            public_path('storage/' . $rutaArchivo),     // public/storage/... (si hay symlink)
            storage_path($rutaArchivo),                 // storage/...
            $rutaArchivo,                               // Ruta absoluta
        ];
        
        foreach ($posiblesRutas as $ruta) {
            if (file_exists($ruta)) {
                \Log::info("Archivo encontrado en: " . $ruta);
                return $ruta;
            }
        }
        
        \Log::error("Archivo no encontrado en ninguna ubicación: " . $rutaArchivo);
        return null;
    }

    private function verificarArchivoExiste($rutaArchivo)
    {
        return $this->obtenerRutaCompleta($rutaArchivo) !== null;
    }
}