<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Usuario extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'usuarios';
    
    protected $fillable = [
        'nombre',
        'apellido_paterno', 
        'apellido_materno',
        'carrera',
        'numero_control',
        'email',
        'contrasena',
        'tipo_usuario'
    ];

    protected $hidden = [
        'contrasena', 'remember_token'
    ];

    // Para Laravel Auth - usar 'contrasena' en lugar de 'password'
    public function getAuthPassword()
    {
        return $this->contrasena;
    }

    // Mantener compatibilidad con tu código existente
    public function isAdmin()
    {
        return $this->tipo_usuario === 'Administrador';
    }

    public function getNombreCompleto()
    {
        return $this->nombre . ' ' . $this->apellido_paterno . ' ' . $this->apellido_materno;
    }
}