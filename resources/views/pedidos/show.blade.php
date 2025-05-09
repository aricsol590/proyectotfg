@extends('layouts.app')

@section('title', 'Detalle del Pedido #' . $pedido->id)

@section('content')
<h1>Detalle del Pedido #{{ $pedido->id }}</h1>

<div class="card mb-3">
    <div class="card-header">Información General</div>
    <div class="card-body">
        <p><strong>ID:</strong> {{ $pedido->id }}</p>
        {{-- <p><strong>Fecha Creación:</strong> {{ $pedido->created_at->format('d/m/Y H:i:s') }}</p> <-- ELIMINA O COMENTA --}}
        {{-- <p><strong>Última Actualización:</strong> {{ $pedido->updated_at->format('d/m/Y H:i:s') }}</p> <-- ELIMINA O COMENTA --}}
        <p><strong>Dirección de Entrega:</strong> {{ $pedido->direccion ?? 'No especificada' }}</p>
        <p><strong>Teléfono de Contacto:</strong> {{ $pedido->telefono ?? 'No especificado' }}</p>
        <p><strong>Repartidor Asignado:</strong> {{ $pedido->repartidor->nombre ?? 'No asignado' }}</p>
    </div>
</div>

<div class="card">
    <div class="card-header">Productos del Pedido</div>
    <div class="card-body">
        @if($pedido->productos->count() > 0)
            <table class="table">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Precio Unitario</th>
                        <th>Cantidad</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @php $totalPedido = 0; @endphp
                    @foreach ($pedido->productos as $producto)
                        @php
                            // Asegúrate que producto->precio existe y es numérico
                            $precioUnitario = is_numeric($producto->precio) ? $producto->precio : 0;
                            // Asegúrate que la cantidad del pivote existe y es numérica
                            $cantidad = isset($producto->pivot->cantidad) && is_numeric($producto->pivot->cantidad) ? $producto->pivot->cantidad : 0;
                            $subtotal = $precioUnitario * $cantidad;
                            $totalPedido += $subtotal;
                        @endphp
                        <tr>
                            <td>{{ $producto->nombre ?? 'Producto no encontrado' }}</td>
                            <td> {{ number_format($precioUnitario, 2) }}€</td>
                            <td>{{ $cantidad }}</td>
                            <td> {{ number_format($subtotal, 2) }}€</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="3" class="text-end">Total Pedido:</th>
                        <th> {{ number_format($totalPedido, 2) }}€</th>
                    </tr>
                </tfoot>
            </table>
        @else
            <p>Este pedido no tiene productos asociados.</p>
        @endif
    </div>
</div>

<div class="mt-3">
    <a href="{{ route('pedidos.index') }}" class="btn btn-secondary">Volver a la lista</a>
    <a href="{{ route('pedidos.edit', $pedido) }}" class="btn btn-warning">Editar Pedido</a>
     <form action="{{ route('pedidos.destroy', $pedido) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('¿Estás seguro de eliminar este pedido?')">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger">Eliminar Pedido</button>
    </form>
</div>

@endsection