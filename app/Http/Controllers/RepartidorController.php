<?php

// app/Http/Controllers/RepartidorController.php

namespace App\Http\Controllers;

use App\Models\Repartidor;
use Illuminate\Http\Request;

class RepartidorController extends Controller
{
    public function index()
    {
        $repartidores = Repartidor::orderBy('nombre')->paginate(15);
        return view('repartidores.index', compact('repartidores'));
    }

    public function create()
    {
        return view('repartidores.create');
    }

    public function store(Request $request)
    {
        // Validación mínima
        $request->validate([
            'nombre'   => 'required|string|max:255|unique:repartidores',
            'telefono' => 'nullable|string|max:20',
        ]);

        Repartidor::create($request->only('nombre', 'telefono'));

        return redirect()->route('repartidores.index')
                         ->with('success', 'Repartidor creado correctamente.');
    }

    public function show(Repartidor $repartidor)
    {
        return view('repartidores.show', compact('repartidor'));
    }

    public function edit(Repartidor $repartidor)
    {
        return view('repartidores.edit', compact('repartidor'));
    }

    public function update(Request $request, Repartidor $repartidor)
    {
        $request->validate([
            'nombre'   => 'required|string|max:255|unique:repartidores,nombre,' . $repartidor->id,
            'telefono' => 'nullable|string|max:20',
        ]);

        $repartidor->update($request->only('nombre', 'telefono'));

        return redirect()->route('repartidores.index')
                         ->with('success', 'Repartidor actualizado correctamente.');
    }

    public function destroy(Repartidor $repartidor)
    {
        // Opcional: evitar borrar si tiene pedidos
        if ($repartidor->pedidos()->exists()) {
            return back()->with('error', 'No se puede eliminar, tiene pedidos asociados.');
        }

        $repartidor->delete();
        return redirect()->route('repartidores.index')
                         ->with('success', 'Repartidor eliminado correctamente.');
    }
}

