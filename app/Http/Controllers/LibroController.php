<?php

namespace App\Http\Controllers;

use App\Models\Libro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class LibroController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (!Auth::check() || !in_array(Auth::user()->tipo_usuario, ['Docente', 'Administrador'])) {
                abort(403, 'Solo los docentes y administradores pueden acceder a esta función.');
            }
            return $next($request);
        });
    }
    public function index(Request $request)
    {
        $orden = $request->get('orden', 'fecha_desc');
        $busqueda = $request->get('busqueda', '');

        $query = Libro::delUsuario(Auth::id());

        // Aplicar búsqueda
        if (!empty($busqueda)) {
            $query->buscar($busqueda);
        }

        // Aplicar ordenamiento
        switch ($orden) {
            case 'titulo_asc':
                $query->orderBy('titulo');
                break;
            case 'titulo_desc':
                $query->orderBy('titulo', 'desc');
                break;
            case 'fecha_asc':
                $query->orderBy('created_at');
                break;
            case 'fecha_desc':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        $libros = $query->paginate(10);
        $totalLibros = $query->count();

        return view('docente.libros.index', compact(
            'libros',
            'totalLibros',
            'orden',
            'busqueda'
        ));
    }

    public function create()
    {
        return view('docente.libros.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'autor' => 'required|string|max:255',
            'anio_publicacion' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'descripcion' => 'nullable|string',
            'isbn' => 'nullable|string|max:20',
            'carrera' => 'required|in:Soporte y Mantenimiento de Equipo de Cómputo,Enfermería General,Ventas,Diseño Gráfico Digital',
            'semestre' => 'required|in:1°,2°,3°,4°,5°,6°',
            'materia' => 'required|string|max:150',
            'archivo_pdf' => 'required|file|mimes:pdf|max:10240' // 10MB máximo
        ]);

        // Procesar archivo PDF
        if ($request->hasFile('archivo_pdf')) {
            $archivo = $request->file('archivo_pdf');
            
            // Generar hash único para el archivo
            $contenido = file_get_contents($archivo->getRealPath());
            $hashArchivo = hash('sha256', $contenido . time());
            
            // Verificar si el archivo ya existe (evitar duplicados)
            $libroExistente = Libro::where('hash_archivo', $hashArchivo)->first();
            if ($libroExistente) {
                return back()->withErrors(['archivo_pdf' => 'Este archivo ya ha sido subido anteriormente.'])->withInput();
            }

            // Generar ruta organizada
            $carpetaCarrera = Str::slug($request->carrera);
            $carpetaSemestre = Str::slug($request->semestre);
            $ruta = "libros/{$carpetaCarrera}/{$carpetaSemestre}";
            
            // Nombre del archivo con hash
            $nombreArchivo = $hashArchivo . '.pdf';
            $rutaCompleta = $archivo->storeAs($ruta, $nombreArchivo, 'public');

            // Crear el registro del libro
            $libro = Libro::create([
                'titulo' => $request->titulo,
                'autor' => $request->autor,
                'anio_publicacion' => $request->anio_publicacion,
                'descripcion' => $request->descripcion,
                'isbn' => $request->isbn,
                'carrera' => $request->carrera,
                'semestre' => $request->semestre,
                'materia' => $request->materia,
                'nombre_archivo' => $archivo->getClientOriginalName(),
                'ruta_archivo' => $rutaCompleta,
                'hash_archivo' => $hashArchivo,
                'tamanio' => $archivo->getSize(),
                'id_usuario' => Auth::id(),
                'descargable' => $request->has('descargable'), // ← Agregar esta línea
            ]);

            return redirect()->route('docente.libros.index')
                ->with('success', 'Libro subido correctamente.');
        }

        return back()->withErrors(['archivo_pdf' => 'Error al subir el archivo.'])->withInput();
    }

    public function edit($id)
    {
        $libro = Libro::delUsuario(Auth::id())->findOrFail($id);
        return view('docente.libros.edit', compact('libro'));
    }

    public function update(Request $request, $id)
    {
        $libro = Libro::delUsuario(Auth::id())->findOrFail($id);

        $request->validate([
            'titulo' => 'required|string|max:255',
            'autor' => 'required|string|max:255',
            'anio_publicacion' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'descripcion' => 'nullable|string',
            'isbn' => 'nullable|string|max:20',
            'carrera' => 'required|in:Soporte y Mantenimiento de Equipo de Cómputo,Enfermería General,Ventas,Diseño Gráfico Digital',
            'semestre' => 'required|in:1°,2°,3°,4°,5°,6°',
            'materia' => 'required|string|max:150',
        ]);

        $libro->update(array_merge($request->only([
            'titulo', 'autor', 'anio_publicacion', 'descripcion', 
            'isbn', 'carrera', 'semestre', 'materia'
        ]), [
            'descargable' => $request->has('descargable') // ← Agregar esta línea
        ]));

        return redirect()->route('docente.libros.index')
            ->with('success', 'Libro actualizado correctamente.');
    }

    public function destroy($id)
    {
        $libro = Libro::delUsuario(Auth::id())->findOrFail($id);
        
        // Eliminar archivo físico
        Storage::disk('public')->delete($libro->ruta_archivo);
        
        // Eliminar registro de la base de datos
        $libro->delete();

        return redirect()->route('docente.libros.index')
            ->with('success', 'Libro eliminado correctamente.');
    }

    public function download($id)
    {
        $libro = Libro::findOrFail($id);
        
        // Verificar si el libro es descargable
        if (!$libro->descargable) {
            abort(403, 'Este libro no está disponible para descarga.');
        }
        
        // Incrementar contador de descargas
        $libro->increment('veces_descargado');
        
        return Storage::disk('public')->download($libro->ruta_archivo, $libro->nombre_archivo);
    }

    // Métodos específicos para administrador
    public function adminIndex(Request $request)
    {
        // Solo administradores pueden acceder
        if (Auth::user()->tipo_usuario !== 'Administrador') {
            abort(403, 'Acceso no autorizado.');
        }
        
        $orden = $request->get('orden', 'fecha_desc');
        $busqueda = $request->get('busqueda', '');
        $carrera = $request->get('carrera', '');
        $semestre = $request->get('semestre', '');
        $docente = $request->get('docente', '');

        $query = Libro::with('usuario');

        // Aplicar búsqueda
        if (!empty($busqueda)) {
            $query->buscar($busqueda);
        }

        // Filtros adicionales para admin
        if (!empty($carrera)) {
            $query->where('carrera', $carrera);
        }
        
        if (!empty($semestre)) {
            $query->where('semestre', $semestre);
        }
        
        if (!empty($docente)) {
            $query->whereHas('usuario', function($q) use ($docente) {
                $q->where('nombre', 'LIKE', "%{$docente}%")
                ->orWhere('email', 'LIKE', "%{$docente}%");
            });
        }

        // Aplicar ordenamiento
        switch ($orden) {
            case 'titulo_asc':
                $query->orderBy('titulo');
                break;
            case 'titulo_desc':
                $query->orderBy('titulo', 'desc');
                break;
            case 'fecha_asc':
                $query->orderBy('created_at');
                break;
            case 'fecha_desc':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        $libros = $query->paginate(20);
        $totalLibros = $query->count();
        
        // Obtener lista de docentes para el filtro
        $docentes = \App\Models\Usuario::where('tipo_usuario', 'Docente')
            ->orderBy('nombre')
            ->pluck('nombre', 'id');

        return view('admin.libros.index', compact(
            'libros',
            'totalLibros',
            'orden',
            'busqueda',
            'carrera',
            'semestre',
            'docente',
            'docentes'
        ));
    }

    public function adminCreate()
    {
        if (Auth::user()->tipo_usuario !== 'Administrador') {
            abort(403, 'Acceso no autorizado.');
        }
        
        return view('admin.libros.create');
    }

    public function adminStore(Request $request)
    {
        if (Auth::user()->tipo_usuario !== 'Administrador') {
            abort(403, 'Acceso no autorizado.');
        }
        
        $request->validate([
            'titulo' => 'required|string|max:255',
            'autor' => 'required|string|max:255',
            'anio_publicacion' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'descripcion' => 'nullable|string',
            'isbn' => 'nullable|string|max:20',
            'carrera' => 'required|in:Soporte y Mantenimiento de Equipo de Cómputo,Enfermería General,Ventas,Diseño Gráfico Digital',
            'semestre' => 'required|in:1°,2°,3°,4°,5°,6°',
            'materia' => 'required|string|max:150',
            'archivo_pdf' => 'required|file|mimes:pdf|max:10240'
        ]);

        if ($request->hasFile('archivo_pdf')) {
            $archivo = $request->file('archivo_pdf');
            
            // Generar hash único para el archivo
            $contenido = file_get_contents($archivo->getRealPath());
            $hashArchivo = hash('sha256', $contenido . time());
            
            // Verificar si el archivo ya existe
            $libroExistente = Libro::where('hash_archivo', $hashArchivo)->first();
            if ($libroExistente) {
                return back()->withErrors(['archivo_pdf' => 'Este archivo ya ha sido subido anteriormente.'])->withInput();
            }

            // Generar ruta organizada
            $carpetaCarrera = Str::slug($request->carrera);
            $carpetaSemestre = Str::slug($request->semestre);
            $ruta = "libros/{$carpetaCarrera}/{$carpetaSemestre}";
            
            // Nombre del archivo con hash
            $nombreArchivo = $hashArchivo . '.pdf';
            $rutaCompleta = $archivo->storeAs($ruta, $nombreArchivo, 'public');

            // Crear el registro del libro
            $libro = Libro::create([
                'titulo' => $request->titulo,
                'autor' => $request->autor,
                'anio_publicacion' => $request->anio_publicacion,
                'descripcion' => $request->descripcion,
                'isbn' => $request->isbn,
                'carrera' => $request->carrera,
                'semestre' => $request->semestre,
                'materia' => $request->materia,
                'nombre_archivo' => $archivo->getClientOriginalName(),
                'ruta_archivo' => $rutaCompleta,
                'hash_archivo' => $hashArchivo,
                'tamanio' => $archivo->getSize(),
                'id_usuario' => Auth::id(), // Admin como responsable
                'descargable' => $request->has('descargable'),
            ]);

            return redirect()->route('admin.libros.index')
                ->with('success', 'Libro subido correctamente como administrador.');
        }

        return back()->withErrors(['archivo_pdf' => 'Error al subir el archivo.'])->withInput();
    }

    public function adminEdit($id)
    {
        if (Auth::user()->tipo_usuario !== 'Administrador') {
            abort(403, 'Acceso no autorizado.');
        }
        
        $libro = Libro::with('usuario')->findOrFail($id);
        return view('admin.libros.edit', compact('libro'));
    }

    public function adminUpdate(Request $request, $id)
    {
        if (Auth::user()->tipo_usuario !== 'Administrador') {
            abort(403, 'Acceso no autorizado.');
        }
        
        $libro = Libro::findOrFail($id);

        $request->validate([
            'titulo' => 'required|string|max:255',
            'autor' => 'required|string|max:255',
            'anio_publicacion' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'descripcion' => 'nullable|string',
            'isbn' => 'nullable|string|max:20',
            'carrera' => 'required|in:Soporte y Mantenimiento de Equipo de Cómputo,Enfermería General,Ventas,Diseño Gráfico Digital',
            'semestre' => 'required|in:1°,2°,3°,4°,5°,6°',
            'materia' => 'required|string|max:150',
        ]);

        $libro->update(array_merge($request->only([
            'titulo', 'autor', 'anio_publicacion', 'descripcion', 
            'isbn', 'carrera', 'semestre', 'materia'
        ]), [
            'descargable' => $request->has('descargable')
        ]));

        return redirect()->route('admin.libros.index')
            ->with('success', 'Libro actualizado correctamente.');
    }

    public function adminDestroy($id)
    {
        if (Auth::user()->tipo_usuario !== 'Administrador') {
            abort(403, 'Acceso no autorizado.');
        }
        
        $libro = Libro::findOrFail($id);
        
        // Eliminar archivo físico
        Storage::disk('public')->delete($libro->ruta_archivo);
        
        // Eliminar registro de la base de datos
        $libro->delete();

        return redirect()->route('admin.libros.index')
            ->with('success', 'Libro eliminado correctamente.');
    }
}