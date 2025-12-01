<?php

namespace App\Http\Controllers;

use App\Models\Libro;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ThumbnailController extends Controller
{
    public function generarThumbnail($libroId)
    {
        $libro = Libro::findOrFail($libroId);
        $thumbnailPath = "thumbnails/{$libroId}.jpg";
        $fullThumbnailPath = storage_path("app/public/{$thumbnailPath}");
        
        // Si ya existe y es reciente (menos de 1 día), devolverlo
        if (Storage::disk('public')->exists($thumbnailPath)) {
            $fileTime = filemtime($fullThumbnailPath);
            if (time() - $fileTime < 86400) { // 1 día en segundos
                return Storage::disk('public')->response($thumbnailPath);
            }
        }
        
        // Verificar que el PDF existe
        $rutaCompleta = $this->obtenerRutaCompleta($libro->ruta_archivo);
        if (!$rutaCompleta || !file_exists($rutaCompleta)) {
            \Log::error("PDF no encontrado para thumbnail: " . $libroId);
            return $this->thumbnailPorDefecto();
        }
        
        // Crear directorio si no existe
        if (!file_exists(dirname($fullThumbnailPath))) {
            mkdir(dirname($fullThumbnailPath), 0755, true);
        }
        
        try {
            // Usar PDF.js en el cliente para generar thumbnails
            // Esta será una solución híbrida
            return $this->thumbnailPorDefecto($libro->titulo);
            
        } catch (\Exception $e) {
            \Log::error("Error generando thumbnail para {$libroId}: " . $e->getMessage());
            return $this->thumbnailPorDefecto($libro->titulo);
        }
    }

    public function obtenerThumbnail($libroId)
    {
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

    private function thumbnailPorDefecto($titulo = null)
    {
        // Generar un SVG con información del libro
        $iniciales = $this->obtenerIniciales($titulo);
        $color = $this->generarColorDesdeTexto($titulo ?? 'PDF');
        
        $svg = '<?xml version="1.0" encoding="UTF-8"?>
        <svg width="120" height="160" viewBox="0 0 120 160" xmlns="http://www.w3.org/2000/svg">
            <rect width="120" height="160" fill="' . $color . '" rx="8" ry="8"/>
            <rect x="2" y="2" width="116" height="156" fill="transparent" stroke="rgba(0,0,0,0.1)" stroke-width="1" rx="8" ry="8"/>
            
            <text x="60" y="70" text-anchor="middle" font-family="Arial, sans-serif" font-size="24" font-weight="bold" fill="white">
                ' . htmlspecialchars($iniciales) . '
            </text>
            
            <text x="60" y="100" text-anchor="middle" font-family="Arial, sans-serif" font-size="10" fill="white" opacity="0.9">
                PDF
            </text>
            
            <rect x="10" y="130" width="100" height="20" fill="rgba(255,255,255,0.2)" rx="4" ry="4"/>
            <text x="60" y="143" text-anchor="middle" font-family="Arial, sans-serif" font-size="8" fill="white">
                CECyTE
            </text>
        </svg>';
        
        return response($svg)->header('Content-Type', 'image/svg+xml');
    }
    
    private function obtenerIniciales($texto)
    {
        if (empty($texto)) return 'PDF';
        
        $palabras = explode(' ', $texto);
        $iniciales = '';
        
        foreach ($palabras as $palabra) {
            if (ctype_alpha($palabra[0] ?? '')) {
                $iniciales .= strtoupper($palabra[0]);
                if (strlen($iniciales) >= 2) break;
            }
        }
        
        return !empty($iniciales) ? $iniciales : 'PDF';
    }
    
    private function generarColorDesdeTexto($texto)
    {
        $hash = md5($texto);
        return sprintf('#%s', substr($hash, 0, 6));
    }
}