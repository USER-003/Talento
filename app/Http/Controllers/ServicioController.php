<?php

namespace App\Http\Controllers;

use App\Models\Servicio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\CategoriasDeServicio;
use App\Models\Comentario;



class ServicioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {   
        $servicios = Servicio::where('id_usuario', Auth::id())->with('categoria')->get();
        return view('servicios.servicio', compact('servicios',));
    }
    
    public function category(Request $request)
    {
        $query = $request->input('query');
        $categoryId = $request->input('categoria_id');
        $categorias = CategoriasDeServicio::all();


        if ($query) {
            $servicios = Servicio::where('id_categoria', $categoryId)
            ->where(function($q) use ($query) {
                $q->whereRaw("similarity(unaccent(lower(nombre_servicio)), unaccent(?)) > 0.2", [$query])
                  ->orWhereRaw("similarity(unaccent(lower(descripcion_servicio)), unaccent(?)) > 0.2", [$query])
                  ->orWhereRaw("unaccent(lower(nombre_servicio)) ILIKE unaccent(?)", ["%{$query}%"])
                  ->orWhereRaw("unaccent(lower(descripcion_servicio)) ILIKE unaccent(?)", ["%{$query}%"]);
            })->get();
        } else {
            $servicios = Servicio::where('id_categoria', $categoryId)->get();
        }

        switch ($categoryId) {
            case '1':
                return view('categorias.tech', compact('servicios', 'query'));
            case '2':
                return view('categorias.negocios', compact('servicios', 'query'));
            case '3':
                return view('categorias.educacion', compact('servicios', 'query'));
            case '4':
                return view('categorias.leyes', compact('servicios', 'query'));
            default:
                return view('index', compact('servicios', 'categorias'));

        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $usuarios = Auth::id();
        return view('servicios.create', compact('usuarios'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
    Servicio::create($request->all());

        return redirect()->route('servicio.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
       //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Servicio $servicio)
    {
        return view('servicios.edit', compact('servicio'));
    }

    public function info(Servicio $servicio)
    {
        $usuario = $servicio->usuario;
        $categoria = $servicio->categoria;
        $servicios = Servicio::all();
    $comentarios = $servicio->comentarios()->with('usuario')->get();
    $promedio = $servicio->averageRating();
    return view('servicios.info', compact('servicio', 'usuario', 'categoria', 'servicios', 'comentarios', 'promedio'));
    }
    
    public function update(Request $request, Servicio $servicio)
    {
        $servicio->update($request->all());
        return redirect()->route('servicio.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Servicio $servicio)
    {
        $servicio->delete();
        return back();
    }

    /**
     * Store or update a comment for a service.
     */
    public function comentar(Request $request, Servicio $servicio)
    {
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comentario' => 'required|string|max:2000',
        ]);

        $userId = Auth::user()->id_usuario;
        Comentario::create([
            'servicio_id' => $servicio->id_servicios_personales,
            'id_usuario' => $userId,
            'rating' => $validated['rating'],
            'comentario' => $validated['comentario'],
        ]);

        return redirect()->route('servicio.info', $servicio)->with('status', 'Comentario guardado');
    }
}
