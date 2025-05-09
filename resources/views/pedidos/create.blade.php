@extends('layouts.app')

@section('title', 'Crear Pedido')

@section('content')
<h1>Crear Nuevo Pedido</h1>

<form action="{{ route('pedidos.store') }}" method="POST" id="create-pedido-form">
    @csrf

    {{-- Paso 1: Seleccionar Productos --}}
    <div class="card mb-4">
        <div class="card-header">Paso 1: Seleccionar Productos</div>
        <div class="card-body">

            <div class="mb-3">
                <h5>Tipos de producto</h5>
                <div class="d-flex flex-wrap gap-2" id="tipo-buttons">
                    @foreach ($productos->pluck('tipo')->unique() as $tipo)
                        <button type="button" class="btn btn-outline-primary tipo-btn" data-tipo="{{ $tipo }}">
                            {{ ucfirst($tipo) }}
                        </button>
                    @endforeach
                </div>
            </div>

            <div class="mb-4">
                <h5>Productos disponibles</h5>
                <div id="productos-container" class="row g-3"></div>
            </div>

            <hr>
            <h5>Productos añadidos</h5>
            <div id="productos-seleccionados" class="mb-3"></div>
            <h4>Total Pedido: <span id="total-pedido"> 0.00</span></h4>
        </div>
    </div>

    {{-- Paso 2: Detalles de Entrega --}}
    <div class="card mb-4">
        <div class="card-header">Paso 2: Detalles de Entrega</div>
        <div class="card-body">
            <div class="mb-3">
                <label class="form-label">Dirección</label>
                <input type="text" name="direccion" class="form-control">
            </div>
            <div class="mb-3">
                <label class="form-label">Teléfono</label>
                <input type="text" name="telefono" class="form-control">
            </div>

            @isset($repartidores)
            <div class="mb-3">
                <label class="form-label">Asignar Repartidor</label>
                <select name="id_repartidor" class="form-select">
                    <option value="">-- Sin asignar --</option>
                    @foreach ($repartidores as $r)
                        <option value="{{ $r->id }}">{{ $r->nombre }}</option>
                    @endforeach
                </select>
            </div>
            @endisset
        </div>
    </div>

    <a href="{{ route('pedidos.index') }}" class="btn btn-secondary">Cancelar</a>
    <button type="submit" class="btn btn-primary">Crear Pedido</button>
</form>

{{-- Fila plantilla para productos seleccionados --}}
<template id="producto-row-template">
    <div class="row align-items-end mb-2 producto-item" data-id="__ID__" data-index="__INDEX__">
        <input type="hidden" name="productos[__INDEX__][id]" value="__ID__">
        <div class="col-md-6">
            <strong>__NOMBRE__</strong>
        </div>
        <div class="col-md-3">
            <input type="number" name="productos[__INDEX__][cantidad]" value="1" min="1" class="form-control cantidad-input">
        </div>
        <div class="col-md-2 text-end precio-unitario"> __PRECIO__</div>
        <div class="col-md-1 text-end">
            <button type="button" class="btn btn-danger btn-sm remove-producto-btn">X</button>
        </div>
    </div>
</template>

{{-- Lista de productos en HTML para evitar usar @json --}}
<div id="todos-los-productos" class="d-none">
    @foreach ($productos as $p)
        <div class="producto-data"
            data-id="{{ $p->id }}"
            data-nombre="{{ $p->nombre }}"
            data-precio="{{ $p->precio }}"
            data-tipo="{{ $p->tipo }}">
        </div>
    @endforeach
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const tipoBtns = document.querySelectorAll('.tipo-btn');
    const productosContainer = document.getElementById('productos-container');
    const productosSeleccionados = document.getElementById('productos-seleccionados');
    const totalPedidoSpan = document.getElementById('total-pedido');
    const template = document.getElementById('producto-row-template');
    const productosDisponibles = document.querySelectorAll('#todos-los-productos .producto-data');
    let productoIndex = 0;

    const productos = Array.from(productosDisponibles).map(el => ({
        id: el.dataset.id,
        nombre: el.dataset.nombre,
        precio: parseFloat(el.dataset.precio),
        tipo: el.dataset.tipo
    }));

    tipoBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            const tipo = btn.getAttribute('data-tipo');
            mostrarProductos(tipo);
        });
    });

    function mostrarProductos(tipo) {
        productosContainer.innerHTML = '';
        const filtrados = productos.filter(p => p.tipo === tipo);
        filtrados.forEach(p => {
            const col = document.createElement('div');
            col.className = 'col-md-3';
            col.innerHTML = `
                <div class="card h-100 producto-card" style="cursor:pointer;" data-id="${p.id}" data-nombre="${p.nombre}" data-precio="${p.precio}">
                    <div class="card-body text-center">
                        <h6 class="card-title mb-1">${p.nombre}</h6>
                        <p class="mb-0"> ${p.precio.toFixed(2)}</p>
                    </div>
                </div>`;
            col.querySelector('.producto-card').addEventListener('click', () => añadirProducto(p));
            productosContainer.appendChild(col);
        });
    }

    function añadirProducto(p) {
        // Evitar duplicados
        if (productosSeleccionados.querySelector(`[data-id="${p.id}"]`)) return;

        const index = productoIndex++;
        let rowHtml = template.innerHTML
            .replace(/__INDEX__/g, index)
            .replace(/__ID__/g, p.id)
            .replace(/__NOMBRE__/g, p.nombre)
            .replace(/__PRECIO__/g, p.precio.toFixed(2));

        const wrapper = document.createElement('div');
        wrapper.innerHTML = rowHtml;
        const row = wrapper.firstElementChild;

        row.querySelector('.cantidad-input').addEventListener('input', actualizarTotal);
        row.querySelector('.remove-producto-btn').addEventListener('click', () => {
            row.remove();
            actualizarTotal();
        });

        productosSeleccionados.appendChild(row);
        actualizarTotal();
    }

    function actualizarTotal() {
        let total = 0;
        productosSeleccionados.querySelectorAll('.producto-item').forEach(row => {
            const precio = parseFloat(row.querySelector('.precio-unitario').textContent.replace('', '').trim());
            const cantidad = parseInt(row.querySelector('.cantidad-input').value);
            if (!isNaN(precio) && !isNaN(cantidad)) {
                total += precio * cantidad; // Asegurarse de que no se multiplica por más de lo debido
            }
        });
        totalPedidoSpan.textContent = ` ${total.toFixed(2)}`;
    }
});
</script>
@endpush
