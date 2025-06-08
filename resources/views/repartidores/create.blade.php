{{-- resources/views/repartidores/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Crear Repartidor')

@section('content')
<div class="container mt-4">
    <h1 class="h3 mb-3">Crear Repartidor</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('repartidores.store') }}" method="POST">
        @csrf

        <div class="form-floating mb-4">
            <input
                type="text"
                name="nombre"
                id="nombre"
                class="form-control @error('nombre') is-invalid @enderror"
                placeholder="Nombre completo"
                value="{{ old('nombre') }}"
                required
            >
            <label for="nombre">Nombre</label>
            @error('nombre')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="d-flex justify-content-end gap-2">
            <a href="{{ route('repartidores.index') }}" class="btn btn-outline-secondary">Cancelar</a>
            <button type="submit" class="btn btn-primary">Guardar</button>
        </div>
    </form>
</div>
@endsection
