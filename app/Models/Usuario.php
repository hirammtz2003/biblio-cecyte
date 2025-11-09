<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

class Usuario extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'usuarios';

    protected $fillable = [
        'numero_control',
        'nombre',
        'apellido_paterno',
        'apellido_materno',
        'email',
        'contrasena',
        'carrera',
        'tipo_usuario',
    ];

    protected $hidden = [
        'contrasena',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Get the password for the user.
     */
    public function getAuthPassword()
    {
        return $this->contrasena;
    }

    /**
     * Relación con los libros subidos por el usuario
     */
    public function libros()
    {
        return $this->hasMany(Libro::class, 'usuario_id');
    }

    /**
     * Verificar si el usuario es administrador
     */
    public function isAdmin()
    {
        return $this->tipo_usuario === 'Administrador';
    }

    /**
     * Verificar si el usuario es docente
     */
    public function isDocente()
    {
        return $this->tipo_usuario === 'Docente';
    }

    /**
     * Verificar si el usuario es alumno
     */
    public function isAlumno()
    {
        return $this->tipo_usuario === 'Alumno';
    }

    /**
     * Obtener el nombre completo del usuario
     */
    public function getNombreCompleto()
    {
        return $this->nombre . ' ' . $this->apellido_paterno . ' ' . $this->apellido_materno;
    }

    // Agregar esta relación al modelo Usuario
    public function librosFavoritos()
    {
        return $this->belongsToMany(Libro::class, 'libro_favorito', 'usuario_id', 'libro_id')
                    ->withTimestamps();
    }

    // Método para verificar si un libro es favorito
    public function tieneFavorito($libroId)
    {
        return $this->librosFavoritos()->where('libro_id', $libroId)->exists();
    }
}