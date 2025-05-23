{{-- resources/views/pedidos/cuentaRepartidor.blade.php --}}
@extends('layouts.app')

@section('title', 'Pedidos últimas 9 horas por repartidor')

@section('header')
    <h1>Cuentas Repartidores/pedidos</h1>
    <p>
        Desde: <strong>{{ $from->format('Y-m-d H:i') }}</strong>
        Hasta: <strong>{{ $to->format('Y-m-d H:i') }}</strong>
    </p>
@endsection

@section('content')
    <div class="row">
        @forelse($pedidos as $repartidorId => $listaPedidos)
            @php
                // Calcular total para este repartidor
                $totalRepartidor = $listaPedidos->reduce(function($carry, $pedido) {
                    return $carry + $pedido->productos->sum(fn($prod) => $prod->pivot->cantidad * $prod->precio);
                }, 0);
            @endphp

            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <strong>
                            Repartidor:
                            {{ optional($listaPedidos->first()->repartidor)->nombre ?? 'Sin asignar' }}
                        </strong>
                        <span class="badge bg-primary">
                            Total: {{ number_format($totalRepartidor, 2) }} €
                        </span>
                    </div>
                    <div class="card-body p-0">
                        @if($listaPedidos->isEmpty())
                            <p class="p-3">No hay pedidos asignados a este repartidor.</p>
                        @else
                            <table class="table table-striped mb-0">
                                <thead>
                                    <tr>
                                        <th># Pedido</th>
                                        <th>Fecha</th>
                                        <th>Dirección</th>
                                        <th>Teléfono</th>
                                        <th>Productos</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($listaPedidos as $pedido)
                                        <tr>
                                            <td>{{ $pedido->id }}</td>
                                            <td>{{ $pedido->created_at->format('Y-m-d H:i') }}</td>
                                            <td>{{ $pedido->direccion ?? '—' }}</td>
                                            <td>{{ $pedido->telefono ?? '—' }}</td>
                                            <td>
                                                <ul class="mb-0 ps-3">
                                                    @foreach($pedido->productos as $producto)
                                                        <li>
                                                            {{ $producto->nombre }}
                                                            × {{ $producto->pivot->cantidad }}
                                                            ({{ number_format($producto->precio * $producto->pivot->cantidad, 2) }}€)
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info">
                    No se han encontrado pedidos en las últimas 9 horas.
                </div>
            </div>
        @endforelse
    </div>
@endsection
