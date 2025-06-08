@extends('layouts.app')

@section('title', 'Lista de Repartidores')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3">Repartidores</h1>
        <a href="{{ route('repartidores.create') }}" class="btn btn-primary btn-sm">
            + Nuevo Repartidor
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <table class="table mb-0">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Teléfono</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($repartidores as $r)
                        <tr>
                            <td>{{ $r->id }}</td>
                            <td>{{ $r->nombre }}</td>
                            <td>{{ $r->telefono ?: '—' }}</td>
                            <td class="text-nowrap">
                                <a href="{{ route('repartidores.show', $r) }}" class="btn btn-info btn-sm me-1">Ver</a>
                                <a href="{{ route('repartidores.edit', $r) }}" class="btn btn-warning btn-sm me-1">Editar</a>
                                <form action="{{ route('repartidores.destroy', $r) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('¿Eliminar este repartidor?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger btn-sm">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-4">No hay repartidores.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3 d-flex justify-content-center">
        {{ $repartidores->links() }}
    </div>
</div>
@endsection
