<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;

class ProductoController extends Controller
{
    /**
     * Mostrar todos los productos, con filtro opcional por tipo.
     */
    public function index(Request $request)
    {
        $tipoFiltro = $request->input('tipo');

        $query = Producto::query();

        if ($tipoFiltro) {
            $query->where('tipo', $tipoFiltro);
        }

        $productos = $query->get();

        $tipos = Producto::select('tipo')->distinct()->pluck('tipo');

        return view('productos.index', compact('productos', 'tipos'));
    }

    /**
     * Mostrar el formulario para crear un nuevo producto.
     */
    public function create()
    {
        return view('productos.create');
    }

    /**
     * Guardar el nuevo producto en la base de datos.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'precio' => 'required|numeric',
            'tipo'   => 'required|string|max:255',
        ]);

        Producto::create([
            'nombre' => $request->input('nombre'),
            'precio' => $request->input('precio'),
            'tipo'   => $request->input('tipo'),
        ]);

        return redirect()
            ->route('productos.index')
            ->with('success', 'Producto creado correctamente.');
    }

    /**
     * Mostrar el formulario para editar un producto existente.
     */
    public function edit(Producto $producto)
    {
        return view('productos.edit', compact('producto'));
    }

    /**
     * Actualizar un producto en la base de datos.
     */
    public function update(Request $request, Producto $producto)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'precio' => 'required|numeric',
            'tipo'   => 'required|string|max:255',
        ]);

        $producto->update([
            'nombre' => $request->input('nombre'),
            'precio' => $request->input('precio'),
            'tipo'   => $request->input('tipo'),
        ]);

        return redirect()
            ->route('productos.index')
            ->with('success', 'Producto actualizado correctamente.');
    }

    /**
     * Eliminar un producto de la base de datos.
     */
    public function delete(Producto $producto)
    {
        $producto->delete();

        return redirect()
            ->route('productos.index')
            ->with('success', 'Producto eliminado correctamente.');
    }

    /**
     * Mostrar los detalles de un producto.
     */
    public function show(Producto $producto)
    {
        return view('productos.show', compact('producto'));
    }
}
