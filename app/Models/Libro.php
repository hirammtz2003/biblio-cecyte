<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Libro extends Model
{
    use HasFactory;

    protected $table = 'libros';
    
    protected $fillable = [
        'titulo',
        'autor',
        'anio_publicacion',
        'descripcion',
        'isbn',
        'carrera',
        'semestre',
        'materia',
        'nombre_archivo',
        'ruta_archivo',
        'hash_archivo',
        'tamanio',
        'id_usuario',
        'activo',
        'descargable',
        'veces_descargado',
        'veces_visto'
    ];

    protected $casts = [
        'anio_publicacion' => 'integer',
        'tamanio' => 'integer',
        'veces_descargado' => 'integer',
        'veces_visto' => 'integer',
        'activo' => 'boolean'
    ];

    // Relación con el usuario (docente) que subió el libro
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario');
    }

    // Scope para búsqueda
    public function scopeBuscar($query, $termino)
    {
        return $query->where(function($q) use ($termino) {
            $q->where('titulo', 'LIKE', "%{$termino}%")
              ->orWhere('autor', 'LIKE', "%{$termino}%")
              ->orWhere('materia', 'LIKE', "%{$termino}%")
              ->orWhere('descripcion', 'LIKE', "%{$termino}%")
              ->orWhere('isbn', 'LIKE', "%{$termino}%");
        });
    }

    // Scope para libros del usuario actual
    public function scopeDelUsuario($query, $usuarioId)
    {
        return $query->where('id_usuario', $usuarioId);
    }

    // Método para obtener la URL pública del archivo
    public function getUrlArchivo()
    {
        return asset('storage/' . $this->ruta_archivo);
    }

    // Método para formatear el tamaño del archivo
    public function getTamanioFormateado()
    {
        $bytes = $this->tamanio;
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }
}