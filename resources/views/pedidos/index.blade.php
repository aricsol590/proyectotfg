@extends('layouts.app')

@section('title', 'Lista de Pedidos')

@section('content')
<div class="container mt-3">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Lista de Pedidos</h1>
        <a href="{{ route('pedidos.create') }}" class="btn btn-outline-primary btn-sm">
            + Nuevo Pedido
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Repartidor</th>
                            <th>Dirección</th>
                            <th>Teléfono</th>
                            <th>Estado</th>
                            <th># Productos</th>
                            <th>Fecha</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pedidos as $pedido)
                            <tr>
                                <td>{{ $pedido->id }}</td>
                                <td>{{ optional($pedido->repartidor)->nombre ?: '—' }}</td>
                                <td>{{ $pedido->direccion ?: '—' }}</td>
                                <td>{{ $pedido->telefono ?: '—' }}</td>
                                <td>{{ ucfirst($pedido->estado) }}</td>
                                <td>{{ $pedido->productos_count }}</td>
                                <td>{{ $pedido->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <div class="d-flex">
                                        <a href="{{ route('pedidos.show', $pedido) }}" class="btn btn-info btn-sm me-2">
                                            Ver
                                        </a>
                                        <a href="{{ route('pedidos.edit', $pedido) }}" class="btn btn-warning btn-sm me-2">
                                            Editar
                                        </a>
                                        <form action="{{ route('pedidos.destroy', $pedido) }}" method="POST" onsubmit="return confirm('¿Eliminar este pedido?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-danger btn-sm">
                                                Eliminar
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">No hay pedidos registrados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-center mt-3">
        {{ $pedidos->links() }}
    </div>
</div>
@endsection
