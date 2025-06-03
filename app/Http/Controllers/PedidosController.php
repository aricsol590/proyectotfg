<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\Producto;
use App\Models\Repartidor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class PedidosController extends Controller
{
    public function index()
    {
        $pedidos = Pedido::with('repartidor')
                         ->withCount('productos')
                         ->orderBy('id', 'desc')
                         ->paginate(15);

        return view('pedidos.index', compact('pedidos'));
    }

    public function create()
    {
        $productos    = Producto::orderBy('tipo')->orderBy('nombre')->get();
        $repartidores = Repartidor::orderBy('nombre')->get();

        return view('pedidos.create', compact('productos', 'repartidores'));
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $pedido = Pedido::create([
                'direccion'     => $request->direccion,
                'telefono'      => $request->telefono,
                'id_repartidor' => $request->id_repartidor,
                'estado'        => 'en proceso',
            ]);

            $attach = [];
            if ($request->has('productos') && is_array($request->productos)) {
                foreach ($request->productos as $p) {
                    if (isset($p['id'], $p['cantidad']) && $p['cantidad'] > 0) {
                        $attach[$p['id']] = ['cantidad' => $p['cantidad']];
                    }
                }
            }

            if (!empty($attach)) {
                $pedido->productos()->attach($attach);
            }

            DB::commit();
            return redirect()->route('pedidos.index')
                             ->with('success', 'Pedido creado exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error al crear pedido: " . $e->getMessage());
            return back()
                ->withInput()
                ->withErrors(['error_general' => 'Error al crear el pedido. ' . $e->getMessage()]);
        }
    }

    public function show(Pedido $pedido)
    {
        $pedido->load('repartidor', 'productos');
        return view('pedidos.show', compact('pedido'));
    }

    public function edit(Pedido $pedido)
    {
        $pedido->load('productos', 'repartidor');
        $productosDisponibles = Producto::orderBy('tipo')->orderBy('nombre')->get();
        $repartidores         = Repartidor::orderBy('nombre')->get();
        $productosActuales    = $pedido->productos->pluck('pivot.cantidad', 'id');

        return view('pedidos.edit', compact(
            'pedido',
            'productosDisponibles',
            'productosActuales',
            'repartidores'
        ));
    }

    public function update(Request $request, Pedido $pedido)
    {
        DB::beginTransaction();
        try {
            $pedido->update([
                'direccion'     => $request->direccion,
                'telefono'      => $request->telefono,
                'id_repartidor' => $request->id_repartidor,
            ]);

            $sync = [];
            if ($request->has('productos') && is_array($request->productos)) {
                foreach ($request->productos as $p) {
                    if (isset($p['id'], $p['cantidad']) && $p['cantidad'] > 0) {
                        $sync[$p['id']] = ['cantidad' => $p['cantidad']];
                    }
                }
            }

            if (!empty($sync)) {
                $pedido->productos()->sync($sync);
            } else {
                $pedido->productos()->detach();
            }

            DB::commit();
            return redirect()->route('pedidos.index')
                             ->with('success', 'Pedido actualizado exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error al actualizar pedido #{$pedido->id}: " . $e->getMessage());
            return back()
                ->withInput()
                ->withErrors(['error_general' => 'Error al actualizar el pedido. ' . $e->getMessage()]);
        }
    }

    public function destroy(Pedido $pedido)
    {
        DB::beginTransaction();
        try {
            $pedido->productos()->detach();
            $pedido->delete();
            DB::commit();
            return redirect()->route('pedidos.index')
                             ->with('success', 'Pedido eliminado exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error al eliminar pedido #{$pedido->id}: " . $e->getMessage());
            return redirect()->route('pedidos.index')
                             ->with('error', 'Error al eliminar el pedido.');
        }
    }

    public function CuentaRepartidor()
    {
        $pedidos = Pedido::with(['repartidor', 'productos'])
            ->whereRaw('created_at BETWEEN DATE_SUB(NOW(), INTERVAL 9 HOUR) AND NOW()')
            ->orderBy('id_repartidor')
            ->get()
            ->groupBy('id_repartidor');

        $nowString = DB::selectOne('SELECT NOW() as now')->now;
        $to        = Carbon::parse($nowString);
        $from      = $to->copy()->subHours(9);

        return view('pedidos.cuentaRepartidor', compact('pedidos', 'from', 'to'));
    }
}
