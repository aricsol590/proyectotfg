@extends('layouts.app')

@section('title', 'Crear Pedido')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3">Crear Nuevo Pedido</h1>
        <a href="{{ route('pedidos.index') }}" class="btn btn-outline-secondary btn-sm">
            ← Volver a la lista
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('pedidos.store') }}" method="POST" id="create-pedido-form">
        @csrf

        {{-- Sección Productos --}}
        <div class="card mb-4">
            <div class="card-header bg-light">
                <strong>Productos del Pedido</strong>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <h5>Tipos de producto</h5>
                    <div class="d-flex flex-wrap gap-2" id="tipo-buttons">
                        @foreach ($productos->pluck('tipo')->unique() as $tipo)
                            <button
                                type="button"
                                class="btn btn-outline-primary btn-sm tipo-btn"
                                data-tipo="{{ $tipo }}"
                            >
                                {{ ucfirst($tipo) }}
                            </button>
                        @endforeach
                    </div>
                </div>

                <div class="mb-3">
                    <h5>Productos disponibles</h5>
                    <div id="productos-container" class="row g-3"></div>
                </div>

                <hr>

                <div class="mb-2">
                    <h5>Productos añadidos</h5>
                </div>
                <div id="productos-seleccionados"></div>

                <div class="mt-3 text-end">
                    <h5>Total: <span id="total-pedido">0.00€</span></h5>
                </div>
            </div>
        </div>

        {{-- Sección Detalles de Entrega --}}
        <div class="card mb-4">
            <div class="card-header bg-light">
                <strong>Detalles de Entrega (Opcional)</strong>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input
                                type="text"
                                name="direccion"
                                id="direccion"
                                class="form-control @error('direccion') is-invalid @enderror"
                                placeholder="Dirección"
                                value="{{ old('direccion') }}"
                            >
                            <label for="direccion">Dirección</label>
                            @error('direccion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating">
                            <input
                                type="text"
                                name="telefono"
                                id="telefono"
                                class="form-control @error('telefono') is-invalid @enderror"
                                placeholder="Teléfono"
                                value="{{ old('telefono') }}"
                            >
                            <label for="telefono">Teléfono</label>
                            @error('telefono')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-floating">
                            <select
                                name="id_repartidor"
                                id="id_repartidor"
                                class="form-select @error('id_repartidor') is-invalid @enderror"
                            >
                                <option value="">-- Asignar --</option>
                                @foreach ($repartidores as $r)
                                    <option value="{{ $r->id }}" {{ old('id_repartidor') == $r->id ? 'selected' : '' }}>
                                        {{ $r->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            <label for="id_repartidor">Repartidor</label>
                            @error('id_repartidor')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2">
            <a href="{{ route('pedidos.index') }}" class="btn btn-outline-secondary">Cancelar</a>
            <button type="submit" class="btn btn-primary">Crear Pedido</button>
        </div>
    </form>
</div>

{{-- Plantilla para fila de producto añadido --}}
<template id="producto-row-template">
    <div class="row mb-3 producto-item align-items-center" data-id="__ID__" data-precio="__PRECIO__">
        <input type="hidden" name="productos[__INDEX__][id]" value="__ID__">
        <div class="col-md-6">
            <strong>__NOMBRE__</strong>
        </div>
        <div class="col-md-3">
            <input
                type="number"
                name="productos[__INDEX__][cantidad]"
                class="form-control cantidad-input"
                min="1"
                value="1"
            >
        </div>
        <div class="col-md-2">
            <input
                type="text"
                class="form-control producto-precio-total"
                readonly
                disabled
                value="0.00€"
            >
        </div>
        <div class="col-md-1 text-end">
            <button type="button" class="btn btn-outline-danger btn-sm remove-producto-btn">&times;</button>
        </div>
    </div>
</template>

{{-- Datos en HTML ocultos --}}
<div id="todos-los-productos" class="d-none">
    @foreach ($productos as $p)
        <div
            class="producto-data"
            data-id="{{ $p->id }}"
            data-nombre="{{ $p->nombre }}"
            data-precio="{{ $p->precio }}"
            data-tipo="{{ $p->tipo }}"
        ></div>
    @endforeach
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const tipoBtns = document.querySelectorAll('.tipo-btn');
    const productosContainer = document.getElementById('productos-container');
    const seleccionados = document.getElementById('productos-seleccionados');
    const totalSpan = document.getElementById('total-pedido');
    const template = document.getElementById('producto-row-template');
    const todosDatos = document.querySelectorAll('#todos-los-productos .producto-data');
    let indexCount = 0;

    const productos = Array.from(todosDatos).map(el => ({
        id: el.dataset.id,
        nombre: el.dataset.nombre,
        precio: parseFloat(el.dataset.precio),
        tipo: el.dataset.tipo
    }));

    tipoBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            tipoBtns.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            mostrarProductos(btn.dataset.tipo);
        });
    });

    function mostrarProductos(tipo) {
        productosContainer.innerHTML = '';
        productos.filter(p => p.tipo === tipo).forEach(p => {
            const col = document.createElement('div');
            col.className = 'col-md-3 mb-3';
            col.innerHTML = `
                <div class="card h-100 producto-card shadow-sm" style="cursor:pointer;"
                     data-id="${p.id}" data-nombre="${p.nombre}" data-precio="${p.precio}">
                    <div class="card-body text-center">
                        <h6 class="card-title mb-1">${p.nombre}</h6>
                        <p class="text-muted mb-0">${p.precio.toFixed(2)}€</p>
                    </div>
                </div>`;
            col.querySelector('.producto-card').addEventListener('click', () => addProducto(p));
            productosContainer.appendChild(col);
        });
    }

    function addProducto(p) {
        if (seleccionados.querySelector(`[data-id="${p.id}"]`)) return;
        const idx = indexCount++;
        let html = template.innerHTML
            .replace(/__INDEX__/g, idx)
            .replace(/__ID__/g, p.id)
            .replace(/__NOMBRE__/g, p.nombre)
            .replace(/__PRECIO__/g, p.precio.toFixed(2));

        const wrapper = document.createElement('div');
        wrapper.innerHTML = html;
        const row = wrapper.firstElementChild;
        bindRowEvents(row);
        seleccionados.appendChild(row);
        recalc();
    }

    function bindRowEvents(row) {
        const qtyInput = row.querySelector('.cantidad-input');
        const removeBtn = row.querySelector('.remove-producto-btn');

        qtyInput.addEventListener('input', () => recalc());
        removeBtn.addEventListener('click', () => {
            row.remove();
            recalc();
        });
        // Set initial subtotal on add
        recalcRow(row);
    }

    function recalcRow(row) {
        const priceUnit = parseFloat(row.dataset.precio) || 0;
        const qty = parseInt(row.querySelector('.cantidad-input').value) || 0;
        const subTotal = (priceUnit * qty).toFixed(2);
        row.querySelector('.producto-precio-total').value = subTotal + '€';
    }

    function recalc() {
        let total = 0;
        seleccionados.querySelectorAll('.producto-item').forEach(row => {
            recalcRow(row);
            const sub = parseFloat(row.querySelector('.producto-precio-total').value.replace('€', '')) || 0;
            total += sub;
        });
        totalSpan.textContent = total.toFixed(2) + '€';
    }

    document.getElementById('create-pedido-form').addEventListener('submit', function(e) {
        if (seleccionados.querySelectorAll('.producto-item').length === 0) {
            e.preventDefault();
            alert('Debes añadir al menos un producto');
        }
    });
});
</script>
@endpush
