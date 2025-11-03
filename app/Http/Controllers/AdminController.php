<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (!Auth::check() || !Auth::user()->isAdmin()) {
                abort(403, 'No tienes permisos de administrador.');
            }
            return $next($request);
        });
    }

    public function index(Request $request)
    {
        // Obtener parámetros de filtro, ordenamiento y búsqueda
        $tipoFiltro = $request->get('tipo', 'todos');
        $carreraFiltro = $request->get('carrera', 'todas');
        $orden = $request->get('orden', 'registro_desc');
        $busqueda = $request->get('busqueda', '');

        // Construir consulta
        $query = Usuario::query();

        // Aplicar búsqueda por nombre o apellidos
        if (!empty($busqueda)) {
            $query->where(function($q) use ($busqueda) {
                $q->where('nombre', 'LIKE', "%{$busqueda}%")
                  ->orWhere('apellido_paterno', 'LIKE', "%{$busqueda}%")
                  ->orWhere('apellido_materno', 'LIKE', "%{$busqueda}%")
                  ->orWhere('numero_control', 'LIKE', "%{$busqueda}%")
                  ->orWhere('email', 'LIKE', "%{$busqueda}%");
            });
        }

        // Aplicar filtros
        if ($tipoFiltro !== 'todos') {
            $query->where('tipo_usuario', $tipoFiltro);
        }

        if ($carreraFiltro !== 'todas') {
            $query->where('carrera', $carreraFiltro);
        }

        // Aplicar ordenamiento
        switch ($orden) {
            case 'nombre_asc':
                $query->orderBy('nombre')->orderBy('apellido_paterno');
                break;
            case 'nombre_desc':
                $query->orderBy('nombre', 'desc')->orderBy('apellido_paterno', 'desc');
                break;
            case 'registro_asc':
                $query->orderBy('created_at');
                break;
            case 'registro_desc':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        $usuarios = $query->paginate(15);
        $totalUsuarios = $usuarios->total();

        return view('admin.usuarios.index', compact(
            'usuarios', 
            'totalUsuarios',
            'tipoFiltro',
            'carreraFiltro',
            'orden',
            'busqueda'
        ));
    }

    public function updateUsuario(Request $request, $id)
    {
        $request->validate([
            'tipo_usuario' => 'required|in:Administrador,Docente,Alumno',
            'carrera' => 'required|in:Soporte y Mantenimiento de Equipo de Cómputo,Enfermería General,Ventas,Diseño Gráfico Digital',
            'admin_password' => 'required'
        ]);

        // Verificar contraseña del administrador
        if (!Hash::check($request->admin_password, Auth::user()->contrasena)) {
            return response()->json([
                'success' => false,
                'message' => 'Contraseña de administrador incorrecta.'
            ], 422);
        }

        $usuario = Usuario::findOrFail($id);
        
        // Prevenir que el último admin se quite sus propios permisos
        if ($usuario->id === Auth::user()->id && $request->tipo_usuario !== 'Administrador') {
            $adminCount = Usuario::where('tipo_usuario', 'Administrador')->count();
            if ($adminCount <= 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'No puedes quitarte los permisos de administrador siendo el único admin.'
                ], 422);
            }
        }

        $usuario->update([
            'tipo_usuario' => $request->tipo_usuario,
            'carrera' => $request->carrera
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Usuario actualizado correctamente.',
            'usuario' => [
                'nombre_completo' => $usuario->getNombreCompleto(),
                'tipo_usuario' => $usuario->tipo_usuario,
                'carrera' => $usuario->carrera
            ]
        ]);
    }

    public function destroyUsuario(Request $request, $id)
    {
        $request->validate([
            'admin_password' => 'required'
        ]);

        // Verificar contraseña del administrador
        if (!Hash::check($request->admin_password, Auth::user()->contrasena)) {
            return response()->json([
                'success' => false,
                'message' => 'Contraseña de administrador incorrecta.'
            ], 422);
        }

        $usuario = Usuario::findOrFail($id);
        
        // Prevenir que el admin se elimine a sí mismo
        if ($usuario->id === Auth::user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'No puedes eliminar tu propia cuenta.'
            ], 422);
        }

        // Verificar si el usuario tiene libros subidos
        $tieneLibros = $usuario->libros()->exists();
        if ($tieneLibros) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede eliminar el usuario porque tiene libros subidos. Elimina primero sus libros.'
            ], 422);
        }

        $nombreUsuario = $usuario->getNombreCompleto();
        $usuario->delete();

        return response()->json([
            'success' => true,
            'message' => "Usuario {$nombreUsuario} eliminado correctamente."
        ]);
    }

    public function getEstadisticas()
    {
        $estadisticas = [
            'total' => Usuario::count(),
            'admins' => Usuario::where('tipo_usuario', 'Administrador')->count(),
            'docentes' => Usuario::where('tipo_usuario', 'Docente')->count(),
            'alumnos' => Usuario::where('tipo_usuario', 'Alumno')->count(),
            'por_carrera' => Usuario::groupBy('carrera')
                ->selectRaw('carrera, COUNT(*) as total')
                ->get()
                ->pluck('total', 'carrera')
        ];

        return response()->json($estadisticas);
    }
}