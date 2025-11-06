<?php

namespace App\Http\Controllers;

use App\Models\Libro;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Response;

class ThumbnailController extends Controller
{
    public function obtenerThumbnail($libroId)
    {
        $libro = Libro::findOrFail($libroId);
        
        // Verificar si ya existe un thumbnail
        $thumbnailPath = "thumbnails/{$libroId}.jpg";
        
        if (Storage::disk('public')->exists($thumbnailPath)) {
            return Storage::disk('public')->response($thumbnailPath);
        }
        
        // Si no existe, generar uno por defecto
        return $this->generarThumbnailPorDefecto($libro);
    }

    private function generarThumbnailPorDefecto($libro)
    {
        // Crear un thumbnail SVG por defecto con información del libro
        $tituloAbreviado = $this->abreviarTexto($libro->titulo, 20);
        $autorAbreviado = $this->abreviarTexto($libro->autor, 15);
        
        $svg = $this->generarSvgThumbnail($tituloAbreviado, $autorAbreviado, $libro->carrera);
        
        // Guardar el SVG como archivo cache
        $thumbnailPath = "thumbnails/{$libro->id}.svg";
        Storage::disk('public')->put($thumbnailPath, $svg);
        
        return response($svg)->header('Content-Type', 'image/svg+xml');
    }

    private function generarSvgThumbnail($titulo, $autor, $carrera = null)
    {
        $colorBase = $this->generarColorDesdeTexto($titulo . $autor);
        $colorClaro = $this->aclararColor($colorBase, 30);
        
        return <<<SVG
<svg width="200" height="280" viewBox="0 0 200 280" xmlns="http://www.w3.org/2000/svg">
    <!-- Fondo del libro -->
    <rect width="200" height="280" fill="{$colorBase}" rx="8"/>
    
    <!-- Lomo del libro -->
    <rect x="160" y="20" width="20" height="240" fill="{$colorClaro}" rx="2"/>
    
    <!-- Portada -->
    <rect x="20" y="20" width="140" height="240" fill="white" rx="4"/>
    
    <!-- Contenido de la portada -->
    <rect x="30" y="40" width="120" height="8" fill="{$colorBase}"/>
    <rect x="30" y="55" width="100" height="5" fill="#666"/>
    <rect x="30" y="65" width="80" height="4" fill="#999"/>
    
    <!-- Título -->
    <text x="100" y="120" text-anchor="middle" font-family="Arial, sans-serif" font-size="12" font-weight="bold" fill="{$colorBase}">
        {$titulo}
    </text>
    
    <!-- Autor -->
    <text x="100" y="140" text-anchor="middle" font-family="Arial, sans-serif" font-size="10" fill="#666">
        {$autor}
    </text>
    
    <!-- Icono de libro -->
    <path d="M80 180 L120 180 L120 200 L80 200 Z M85 185 L115 185 L115 195 L85 195 Z" fill="{$colorBase}"/>
    <path d="M85 185 L85 195 M95 185 L95 195 M105 185 L105 195 M115 185 L115 195" stroke="{$colorBase}" stroke-width="1"/>
    
    <!-- Carrera (si existe) -->
    <text x="100" y="230" text-anchor="middle" font-family="Arial, sans-serif" font-size="8" fill="#666">
        {$carrera}
    </text>
    
    <!-- Efecto de páginas -->
    <path d="M20 20 L25 15 L25 265 L20 260 Z" fill="{$colorClaro}" opacity="0.6"/>
</svg>
SVG;
    }

    private function abreviarTexto($texto, $maxLength)
    {
        if (strlen($texto) <= $maxLength) {
            return $texto;
        }
        
        return substr($texto, 0, $maxLength - 3) . '...';
    }

    private function generarColorDesdeTexto($texto)
    {
        $hash = md5($texto);
        
        // Generar colores pastel a partir del hash
        $r = hexdec(substr($hash, 0, 2)) % 128 + 100;  // 100-228
        $g = hexdec(substr($hash, 2, 2)) % 128 + 100;
        $b = hexdec(substr($hash, 4, 2)) % 128 + 100;
        
        return "rgb({$r},{$g},{$b})";
    }

    private function aclararColor($colorBase, $porcentaje)
    {
        // Extraer valores RGB
        preg_match('/rgb\((\d+),(\d+),(\d+)\)/', $colorBase, $matches);
        $r = min(255, $matches[1] + $porcentaje);
        $g = min(255, $matches[2] + $porcentaje);
        $b = min(255, $matches[3] + $porcentaje);
        
        return "rgb({$r},{$g},{$b})";
    }
}