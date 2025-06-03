<!-- resources/views/productos/edit.blade.php -->
@extends('layouts.app')

@section('title', 'Editar Producto')

@section('content')
<div class="container mt-4">
    <div class="row mb-3">
        <div class="col">
            <h1>Editar Producto</h1>
        </div>
        <div class="col-auto">
            <a href="{{ route('productos.index') }}" class="btn btn-outline-primary">
                ← Volver a la lista
            </a>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-6">
            <form action="{{ route('productos.update', $producto) }}" method="POST" class="needs-validation" novalidate>
                @csrf
                @method('PUT')

                <div class="form-group mb-3">
                    <label for="nombre" class="form-label">Nombre</label>
                    <input
                        type="text"
                        class="form-control @error('nombre') is-invalid @enderror"
                        name="nombre"
                        id="nombre"
                        value="{{ old('nombre', $producto->nombre) }}"
                        required
                    >
                    @error('nombre')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @else
                        <div class="form-text">Máximo 255 caracteres.</div>
                    @enderror
                </div>

                <div class="form-group mb-3">
                    <label for="precio" class="form-label">Precio</label>
                    <input
                        type="number"
                        step="0.01"
                        class="form-control @error('precio') is-invalid @enderror"
                        name="precio"
                        id="precio"
                        value="{{ old('precio', $producto->precio) }}"
                        required
                    >
                    @error('precio')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @else
                        <div class="form-text">Ejemplo: 9.99</div>
                    @enderror
                </div>

                <div class="form-group mb-4">
                    <label for="tipo" class="form-label">Tipo</label>
                    <input
                        type="text"
                        class="form-control @error('tipo') is-invalid @enderror"
                        name="tipo"
                        id="tipo"
                        value="{{ old('tipo', $producto->tipo) }}"
                        required
                    >
                    @error('tipo')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @else
                        <div class="form-text">Categoría o familia del producto.</div>
                    @enderror
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">
                        Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
