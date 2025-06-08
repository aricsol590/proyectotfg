@extends('layouts.app')

@section('title', 'Repartidor #' . $repartidor->id)

@section('content')
<div class="container mt-4">
    <div class="card shadow-sm">
        <div class="card-header">
            <h2 class="mb-0">Repartidor #{{ $repartidor->id }}</h2>
        </div>
        <div class="card-body">
            <p><strong>Nombre:</strong> {{ $repartidor->nombre }}</p>
            <p><strong>Teléfono:</strong> {{ $repartidor->telefono ?: '—' }}</p>
            <p><strong>Creado:</strong> {{ $repartidor->created_at->format('d/m/Y H:i') }}</p>
            <p><strong>Actualizado:</strong> {{ $repartidor->updated_at->format('d/m/Y H:i') }}</p>
        </div>
    </div>

    <div class="mt-3">
        <a href="{{ route('repartidores.index') }}" class="btn btn-outline-secondary">Volver</a>
        <a href="{{ route('repartidores.edit', $repartidor) }}" class="btn btn-primary">Editar</a>
    </div>
</div>
@endsection
