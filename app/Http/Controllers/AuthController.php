<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        // Adaptar tu lógica de login existente
        $usuario = Usuario::where('email', $request->email)->first();

        if ($usuario && Hash::check($request->password, $usuario->contrasena)) {
            Auth::login($usuario);
            $request->session()->regenerate();
            
            return redirect()->intended('/dashboard');
        }

        return back()->withErrors([
            'email' => 'Las credenciales no son válidas.',
        ]);
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
{
    $request->validate([
        'nombre' => 'required|string|max:100',
        'apellido_paterno' => 'required|string|max:100',
        'apellido_materno' => 'required|string|max:100',
        'carrera' => 'required|in:Soporte y Mantenimiento de Equipo de Cómputo,Enfermería General,Ventas,Diseño Gráfico Digital',
        'numero_control' => 'required|string|max:20|unique:usuarios',
        'email' => 'required|email|unique:usuarios',
        'password' => 'required|min:6|confirmed',
    ]);

    $usuario = Usuario::create([
        'nombre' => $request->nombre,
        'apellido_paterno' => $request->apellido_paterno,
        'apellido_materno' => $request->apellido_materno,
        'carrera' => $request->carrera,
        'numero_control' => $request->numero_control,
        'email' => $request->email,
        'contrasena' => Hash::make($request->password),
        'tipo_usuario' => 'Alumno', // Por defecto
    ]);

    Auth::login($usuario);

    return redirect('/dashboard')->with('success', '¡Registro exitoso! Bienvenido a la biblioteca virtual.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}