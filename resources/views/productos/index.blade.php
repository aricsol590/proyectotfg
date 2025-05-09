@extends('layouts.app')

@section('title', 'Lista de Productos')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Lista de Productos</h1>
        <a href="{{ route('productos.create') }}" class="btn btn-primary">Crear Nuevo Producto</a>
    </div>

    {{-- Filtro por tipo --}}
    <form method="GET" class="row g-3 mb-4">
        <div class="col-md-4">
            <label for="tipo" class="form-label">Filtrar por tipo</label>
            <select name="tipo" id="tipo" class="form-select" onchange="this.form.submit()">
                <option value="">-- Todos los Tipos --</option>
                @foreach ($tipos as $t)
                    <option value="{{ $t }}" {{ request('tipo') == $t ? 'selected' : '' }}>
                        {{ ucfirst($t) }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-auto align-self-end">
            @if(request('tipo'))
                <a href="{{ route('productos.index') }}" class="btn btn-secondary">Limpiar</a>
            @endif
        </div>
    </form>

    {{-- Mensajes Flash --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Tabla de productos --}}
    <table class="table table-striped table-hover">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Precio</th>
                <th>Tipo</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($productos as $producto)
                <tr>
                    <td>{{ $producto->id }}</td>
                    <td>{{ $producto->nombre }}</td>
                    <td>{{ $producto->precio }}</td>
                    <td>{{ ucfirst($producto->tipo) }}</td>
                    <td>
                        <a href="{{ route('productos.show', $producto) }}" class="btn btn-sm btn-info">Ver</a>
                        <a href="{{ route('productos.edit', $producto) }}" class="btn btn-sm btn-warning">Editar</a>
                        <form action="{{ route('productos.delete', $producto) }}" method="POST" style="display:inline;" onsubmit="return confirm('¿Estás seguro de eliminar este producto?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">Eliminar</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">No hay productos registrados.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection
