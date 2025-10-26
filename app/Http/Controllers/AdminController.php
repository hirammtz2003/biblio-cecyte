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
        // Aplicar middleware de autenticación
        $this->middleware('auth');
        
        // Verificar que el usuario sea administrador
        $this->middleware(function ($request, $next) {
            if (!Auth::check() || !Auth::user()->isAdmin()) {
                abort(403, 'No tienes permisos de administrador.');
            }
            return $next($request);
        });
    }

    public function index(Request $request)
    {
        // Obtener parámetros de filtro y ordenamiento
        $tipoFiltro = $request->get('tipo', 'todos');
        $carreraFiltro = $request->get('carrera', 'todas');
        $orden = $request->get('orden', 'registro_desc');

        // Construir consulta
        $query = Usuario::query();

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

        $usuarios = $query->get();
        $totalUsuarios = $usuarios->count();

        return view('admin.usuarios.index', compact(
            'usuarios', 
            'totalUsuarios',
            'tipoFiltro',
            'carreraFiltro',
            'orden'
        ));
    }

    public function updateTipoUsuario(Request $request, $id)
    {
        $request->validate([
            'tipo_usuario' => 'required|in:Administrador,Docente,Alumno',
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

        $usuario->update(['tipo_usuario' => $request->tipo_usuario]);

        return response()->json([
            'success' => true,
            'message' => 'Tipo de usuario actualizado correctamente.',
            'usuario' => [
                'nombre_completo' => $usuario->getNombreCompleto(),
                'tipo_usuario' => $usuario->tipo_usuario
            ]
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