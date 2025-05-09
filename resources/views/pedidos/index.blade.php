@extends('layouts.app')

@section('title', 'Lista de Pedidos')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1>Lista de Pedidos</h1>
    <a href="{{ route('pedidos.create') }}" class="btn btn-primary">Crear Nuevo Pedido</a>
</div>

{{-- Mensajes Flash --}}
@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<table class="table table-striped table-hover">
    <thead>
        <tr>
            <th>ID</th>
            <th>Repartidor</th>
            <th>Dirección</th>
            <th>Teléfono</th>
            <th>Estado</th> {{-- NUEVA COLUMNA --}}
            <th>Nº Productos</th>
            {{-- <th>Fecha Creación</th> <-- ELIMINA O COMENTA ESTA LÍNEA --}}
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($pedidos as $pedido)
            <tr>
                <td>{{ $pedido->id }}</td>
                <td>{{ $pedido->repartidor->nombre ?? 'No asignado' }}</td>
                <td>{{ $pedido->direccion ?? '-' }}</td>
                <td>{{ $pedido->telefono ?? '-' }}</td>
                <td>{{ ucfirst($pedido->estado) }}</td> {{-- NUEVA COLUMNA --}}
                <td>{{ $pedido->productos_count }}</td>
                {{-- <td>{{ $pedido->created_at->format('d/m/Y H:i') }}</td> <-- ELIMINA O COMENTA ESTA LÍNEA --}}
                <td>
                    <a href="{{ route('pedidos.show', $pedido) }}" class="btn btn-sm btn-info">Ver</a>
                    <a href="{{ route('pedidos.edit', $pedido) }}" class="btn btn-sm btn-warning">Editar</a>
                    <form action="{{ route('pedidos.destroy', $pedido) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('¿Estás seguro de eliminar este pedido?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger">Eliminar</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr>
                {{-- Ajusta el colspan si eliminaste la columna de fecha --}}
                <td colspan="7" class="text-center">No hay pedidos registrados.</td>
            </tr>
        @endforelse
    </tbody>
</table>

{{-- Paginación --}}
<div class="d-flex justify-content-center">
    {{ $pedidos->links() }}
</div>

@endsection
