<?php

namespace App\Http\Controllers;

use App\Models\Libro;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class ThumbnailController extends Controller
{
    public function generarThumbnail($libroId)
    {
        \Log::info("Generando thumbnail para libro: " . $libroId);
        
        $libro = Libro::findOrFail($libroId);
        $rutaCompleta = $this->obtenerRutaCompleta($libro->ruta_archivo);
        
        \Log::info("Ruta del PDF: " . $rutaCompleta);
        \Log::info("¿Existe el archivo?: " . (file_exists($rutaCompleta) ? 'Sí' : 'No'));

        $thumbnailPath = storage_path("app/public/thumbnails/{$libroId}.jpg");
        \Log::info("Ruta del thumbnail: " . $thumbnailPath);

        // Crear directorio si no existe
        if (!file_exists(dirname($thumbnailPath))) {
            mkdir(dirname($thumbnailPath), 0755, true);
        }

        // Solo intentar generar si el PDF existe
        if (file_exists($rutaCompleta)) {
            try {
                \Log::info("Intentando generar thumbnail con Spatie...");
                
                $pdf = new \Spatie\PdfToImage\Pdf($rutaCompleta);
                
                // Para la versión 1.2.2, prueba estos métodos
                if (method_exists($pdf, 'setPage')) {
                    $pdf->setPage(1);
                }
                
                if (method_exists($pdf, 'setOutputFormat')) {
                    $pdf->setOutputFormat('jpg');
                }
                
                // Intentar guardar la imagen
                $result = $pdf->saveImage($thumbnailPath);
                \Log::info("Resultado de saveImage: " . ($result ? 'Éxito' : 'Fallo'));
                
                if (file_exists($thumbnailPath)) {
                    \Log::info("Thumbnail generado exitosamente");
                    return response()->file($thumbnailPath);
                } else {
                    \Log::error("Thumbnail no se generó pero no hubo error");
                }
                
            } catch (\Exception $e) {
                \Log::error("Error en Spatie: " . $e->getMessage());
            }
        }

        \Log::info("Usando thumbnail por defecto");
        return $this->thumbnailPorDefecto();
    }

    public function obtenerThumbnail($libroId)
    {
        $thumbnailPath = "thumbnails/{$libroId}.jpg";
        
        if (Storage::disk('public')->exists($thumbnailPath)) {
            \Log::info("Thumbnail encontrado en cache: " . $libroId);
            return Storage::disk('public')->response($thumbnailPath);
        }
        
        \Log::info("Thumbnail no encontrado, generando: " . $libroId);
        return $this->generarThumbnail($libroId);
    }

    private function obtenerRutaCompleta($rutaArchivo)
    {
        $posiblesRutas = [
            storage_path('app/' . $rutaArchivo),
            storage_path('app/public/' . $rutaArchivo),
            public_path('storage/' . $rutaArchivo),
            $rutaArchivo,
        ];
        
        foreach ($posiblesRutas as $ruta) {
            if (file_exists($ruta)) {
                return $ruta;
            }
        }
        
        return null;
    }

    private function thumbnailPorDefecto()
    {
        $svg = '<?xml version="1.0" encoding="UTF-8"?>
        <svg width="120" height="160" viewBox="0 0 120 160" xmlns="http://www.w3.org/2000/svg">
            <rect width="120" height="160" fill="#f8f9fa" stroke="#dee2e6" stroke-width="1"/>
            <path d="M40 60H80V75H40V60ZM60 90C54.4772 90 50 85.5228 50 80C50 74.4772 54.4772 70 60 70C65.5228 70 70 74.4772 70 80C70 85.5228 65.5228 90 60 90Z" fill="#6c757d"/>
            <text x="60" y="120" text-anchor="middle" font-family="Arial" font-size="12" fill="#6c757d">PDF</text>
        </svg>';
        
        return response($svg)->header('Content-Type', 'image/svg+xml');
    }
}