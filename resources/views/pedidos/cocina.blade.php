{{-- resources/views/pedidos/cocina.blade.php --}}
@extends('layouts.app')

@section('title', 'Cocina')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3">Cocina: Pedidos en Proceso</h1>
        <a href="{{ route('pedidos.index') }}" class="btn btn-outline-secondary btn-sm">
            ← Volver a Pedidos
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @forelse($pedidos as $pedido)
        <div class="card mb-3 shadow-sm">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-1">Pedido #{{ $pedido->id }}</h5>
                    <p class="mb-0">
                        <strong>Creado:</strong> {{ $pedido->created_at->format('d/m/Y H:i') }}<br>
                        <strong>Estado:</strong> {{ ucfirst($pedido->estado) }}<br>
                        <strong>Dirección:</strong> {{ $pedido->direccion ?: '—' }}
                    </p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('pedidos.show', $pedido) }}"
                       class="btn btn-info btn-sm">
                        Ver
                    </a>
                    <a href="{{ route('pedidos.edit', $pedido) }}"
                       class="btn btn-warning btn-sm">
                        Editar
                    </a>
                    <form action="{{ route('pedidos.avanzarEstado', $pedido) }}" method="POST" class="m-0">
                        @csrf
                        <button type="submit" class="btn btn-success btn-sm">
                            Enviar al horno
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @empty
        <div class="alert alert-info">
            No hay pedidos en proceso.
        </div>
    @endforelse
</div>
@endsection
