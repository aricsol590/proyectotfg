<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\Producto;
use App\Models\Repartidor; // <-- ¡Importante importar Repartidor!
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class PedidosController extends Controller
{
    // ... index() ...
    public function index()
    {
        $pedidos = Pedido::with('repartidor') // Cargar repartidor para mostrar en la lista
                         ->withCount('productos')
                         ->orderBy('id', 'desc')
                         ->paginate(15);

        return view('pedidos.index', compact('pedidos'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $productos = Producto::orderBy('nombre')->get();
        $repartidores = Repartidor::orderBy('nombre')->get(); // <-- Cargar repartidores

        // Pasar ambas variables a la vista
        return view('pedidos.create', compact('productos', 'repartidores'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'productos' => 'required|array|min:1',
            'productos.*.id' => 'required|integer|exists:productos,id',
            'productos.*.cantidad' => 'required|integer|min:1',
            'direccion' => 'nullable|string|max:255',
            'telefono' => 'nullable|string|max:20',
            // Añadir validación para repartidor (opcional)
            'id_repartidor' => 'nullable|integer|exists:repartidores,id', // nullable permite no seleccionar, exists valida si se selecciona
        ], [
            // ... (mensajes de error anteriores) ...
            'id_repartidor.exists' => 'El repartidor seleccionado no es válido.',
        ]);

        DB::beginTransaction();
        try {
            $pedido = Pedido::create([
                'direccion' => $request->input('direccion'),
                'telefono' => $request->input('telefono'),
                'id_repartidor' => $request->input('id_repartidor'), // <-- Guardar el ID del repartidor seleccionado (será null si no se selecciona)
            ]);

            $productosParaAdjuntar = [];
            foreach ($request->input('productos') as $productoData) {
                 if (isset($productoData['id']) && $productoData['id'] > 0 && isset($productoData['cantidad']) && $productoData['cantidad'] > 0) {
                    $productosParaAdjuntar[$productoData['id']] = ['cantidad' => $productoData['cantidad']];
                 }
            }

            if (!empty($productosParaAdjuntar)) {
                $pedido->productos()->attach($productosParaAdjuntar);
            } else {
                 throw new \Exception("No se proporcionaron productos válidos para el pedido.");
            }

            DB::commit();

            return redirect()->route('pedidos.index')->with('success', 'Pedido creado exitosamente.');

        } catch (\Exception $e) {
            // ... (catch como antes) ...
            DB::rollBack();
            Log::error("Error al crear pedido: " . $e->getMessage());
            return redirect()->back()
                             ->withInput()
                             ->withErrors(['error_general' => 'Error al crear el pedido. Verifica los datos. Detalles: ' . $e->getMessage()]);
        }
    }

    // ... show() ...
     public function show(Pedido $pedido)
    {
        $pedido->load('repartidor', 'productos'); // Asegurarse de cargar el repartidor
        return view('pedidos.show', compact('pedido'));
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Pedido $pedido)
    {
        $pedido->load('productos', 'repartidor'); // Cargar datos actuales
        $productosDisponibles = Producto::orderBy('nombre')->get();
        $repartidores = Repartidor::orderBy('nombre')->get(); // <-- Cargar todos los repartidores
        $productosActuales = $pedido->productos->pluck('pivot.cantidad', 'id');

        // Pasar todas las variables necesarias a la vista
        return view('pedidos.edit', compact('pedido', 'productosDisponibles', 'productosActuales', 'repartidores'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Pedido $pedido)
    {
        $request->validate([
            'productos' => 'required|array|min:1',
            'productos.*.id' => 'required|integer|exists:productos,id',
            'productos.*.cantidad' => 'required|integer|min:1',
            'direccion' => 'nullable|string|max:255',
            'telefono' => 'nullable|string|max:20',
            // Añadir validación para repartidor (opcional)
            'id_repartidor' => 'nullable|integer|exists:repartidores,id',
        ], [
            // ... (mensajes de error anteriores) ...
             'id_repartidor.exists' => 'El repartidor seleccionado no es válido.',
        ]);

        DB::beginTransaction();
        try {
            $pedido->update([
                'direccion' => $request->input('direccion'),
                'telefono' => $request->input('telefono'),
                'id_repartidor' => $request->input('id_repartidor'), // <-- Actualizar el ID del repartidor
            ]);

            $productosParaSincronizar = [];
            foreach ($request->input('productos') as $productoData) {
                 if (isset($productoData['id']) && $productoData['id'] > 0 && isset($productoData['cantidad']) && $productoData['cantidad'] > 0) {
                     $productosParaSincronizar[$productoData['id']] = ['cantidad' => $productoData['cantidad']];
                 }
            }

             if (!empty($productosParaSincronizar)) {
                $pedido->productos()->sync($productosParaSincronizar);
             } else {
                 $pedido->productos()->detach();
             }

            DB::commit();

            return redirect()->route('pedidos.index')->with('success', 'Pedido actualizado exitosamente.');

        } catch (\Exception $e) {
            // ... (catch como antes) ...
             DB::rollBack();
             Log::error("Error al actualizar pedido #{$pedido->id}: " . $e->getMessage());
            return redirect()->back()
                             ->withInput()
                             ->withErrors(['error_general' => 'Error al actualizar el pedido. Verifica los datos. Detalles: ' . $e->getMessage()]);
        }
    }

    // ... destroy() ...
     public function destroy(Pedido $pedido)
    {
        DB::beginTransaction();
        try {
            $pedido->productos()->detach();
            $pedido->delete();
            DB::commit();
            return redirect()->route('pedidos.index')->with('success', 'Pedido eliminado exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error al eliminar pedido #{$pedido->id}: " . $e->getMessage());
            return redirect()->route('pedidos.index')->with('error', 'Error al eliminar el pedido.');
        }
    }
}