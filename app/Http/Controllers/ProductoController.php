<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;

class ProductoController extends Controller
{
    // Mostrar todos los productos con filtro opcional por tipo
    public function index(Request $request)
    {
        $tipoFiltro = $request->input('tipo');

        $query = Producto::query();

        if ($tipoFiltro) {
            $query->where('tipo', $tipoFiltro);
        }

        $productos = $query->get();

        // Obtener tipos únicos para el filtro (sin repetir)
        $tipos = Producto::select('tipo')->distinct()->pluck('tipo');

        return view('productos.index', compact('productos', 'tipos'));
    }

    // Mostrar el formulario para crear un nuevo producto
    public function create()
    {
        return view('productos.create');
    }

    // Guardar el nuevo producto
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'precio' => 'required|numeric',
            'tipo' => 'required|string|max:255',
        ]);

        Producto::create($request->all());

        return redirect()->route('productos.index')->with('success', 'Producto creado correctamente.');
    }

    // Mostrar el formulario para editar un producto
    public function edit(Producto $producto)
    {
        return view('productos.edit', compact('producto'));
    }

    // Actualizar el producto
    public function update(Request $request, Producto $producto)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'precio' => 'required|numeric',
            'tipo' => 'required|string|max:255',
        ]);

        $producto->update($request->all());

        return redirect()->route('productos.index')->with('success', 'Producto actualizado correctamente.');
    }

    // Eliminar un producto
    public function delete(Producto $producto)
    {
        $producto->delete();

        return redirect()->route('productos.index')->with('success', 'Producto eliminado correctamente.');
    }

    // Mostrar los detalles de un producto
    public function show(Producto $producto)
    {
        return view('productos.show', compact('producto'));
    }
}
