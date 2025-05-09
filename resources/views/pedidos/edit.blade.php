@extends('layouts.app')

@section('title', 'Editar Pedido #' . $pedido->id)

@section('content')
<h1>Editar Pedido #{{ $pedido->id }}</h1>

{{-- Mostrar errores de validación generales del backend --}}
@if ($errors->any() && !$errors->has('productos.*') && !$errors->has('id_repartidor'))
    <div class="alert alert-danger">
        <strong>¡Error!</strong> Por favor corrige los siguientes errores:
        <ul>
            @foreach ($errors->all() as $error)
                 @if (!Str::startsWith($error, 'productos.'))
                    <li>{{ $error }}</li>
                 @endif
            @endforeach
        </ul>
    </div>
@endif
@error('error_general') {{-- Mostrar error general del catch del controlador --}}
    <div class="alert alert-danger">{{ $message }}</div>
@enderror

<form action="{{ route('pedidos.update', $pedido) }}" method="POST" id="edit-pedido-form">
    @csrf
    @method('PUT') {{-- Importante para la actualización --}}

    {{-- Sección de Selección de Productos --}}
    <div class="card mb-4">
        <div class="card-header">
           Editar Productos del Pedido
        </div>
        <div class="card-body">
            <div id="productos-seleccionados">
                {{-- Llenar con productos existentes o con old() si hay error --}}
                @php
                    // Convertir colección a array simple para el bucle si no hay old()
                    $productosEnFormulario = old('productos', $productosActuales->map(function($cantidad, $id) {
                        return ['id' => $id, 'cantidad' => $cantidad];
                    })->values()->all());
                @endphp

                @foreach($productosEnFormulario as $index => $productoData)
                    {{-- Asegurarse que los datos necesarios existen --}}
                     @if(isset($productoData['id']) && isset($productoData['cantidad']))
                    <div class="row align-items-end mb-3 producto-item" data-index="{{ $index }}">
                        <div class="col-md-6">
                            <label class="form-label">Producto</label>
                            {{-- Usar $productosDisponibles para la lista completa --}}
                            <select name="productos[{{ $index }}][id]" class="form-select producto-select @error('productos.' . $index . '.id') is-invalid @enderror">
                                <option value="">Seleccione un producto...</option>
                                @isset($productosDisponibles)
                                    @foreach ($productosDisponibles as $producto)
                                        {{-- Preseleccionar old() o el valor actual del $productoData --}}
                                        <option value="{{ $producto->id }}" data-precio="{{ $producto->precio }}" {{ ($productoData['id'] == $producto->id) ? 'selected' : '' }}>
                                            {{ $producto->nombre }} ({{ number_format($producto->precio, 2) }}€)
                                        </option>
                                    @endforeach
                                @endisset
                            </select>
                            @error('productos.' . $index . '.id')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @else
                                <div class="invalid-feedback d-block validation-placeholder-id"></div>
                            @enderror
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Cantidad</label>
                            {{-- Usar el valor de $productoData (que viene de old() o de $productosActuales) --}}
                            <input type="number" name="productos[{{ $index }}][cantidad]" class="form-control producto-cantidad @error('productos.' . $index . '.cantidad') is-invalid @enderror" min="1" value="{{ $productoData['cantidad'] ?? 1 }}" required>
                             @error('productos.' . $index . '.cantidad')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @else
                                <div class="invalid-feedback d-block validation-placeholder-cantidad"></div>
                            @enderror
                        </div>
                         <div class="col-md-2">
                            <label class="form-label">Subtotal</label>
                            <input type="text" class="form-control producto-precio-total" readonly disabled>
                        </div>
                        <div class="col-md-1">
                            <button type="button" class="btn btn-danger btn-sm remove-producto-btn" title="Quitar producto">X</button>
                        </div>
                    </div>
                     @endif
                @endforeach
            </div>
            <button type="button" id="add-producto-btn" class="btn btn-success mt-2">Añadir Producto</button>
             <hr>
             <h4>Total Pedido: <span id="total-pedido">0.00€</span></h4>
             {{-- Mostrar errores generales relacionados con el array de productos --}}
              @if ($errors->has('productos') && !$errors->has('productos.*'))
                 <div class="alert alert-danger mt-2">{{ $errors->first('productos') }}</div>
             @endif
             @if ($errors->has('productos.*.id') || $errors->has('productos.*.cantidad'))
                 <div class="alert alert-danger mt-2">Por favor, revisa los productos. Asegúrate de seleccionar un producto y cantidad válida (mínimo 1) en cada fila.</div>
             @endif
        </div>
    </div>

    {{-- Sección de Dirección, Teléfono y Repartidor --}}
    <div class="card mb-4">
        <div class="card-header">
            Detalles de Entrega (Opcional)
        </div>
        <div class="card-body">
            <div class="mb-3">
                <label for="direccion" class="form-label">Dirección</label>
                <input type="text" class="form-control @error('direccion') is-invalid @enderror" id="direccion" name="direccion" value="{{ old('direccion', $pedido->direccion) }}">
                 @error('direccion')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="telefono" class="form-label">Teléfono</label>
                <input type="text" class="form-control @error('telefono') is-invalid @enderror" id="telefono" name="telefono" value="{{ old('telefono', $pedido->telefono) }}">
                 @error('telefono')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Selector de Repartidor --}}
             @isset($repartidores)
            <div class="mb-3">
                <label for="id_repartidor" class="form-label">Asignar Repartidor (Opcional)</label>
                <select class="form-select @error('id_repartidor') is-invalid @enderror" id="id_repartidor" name="id_repartidor">
                    <option value="">-- Sin asignar --</option>
                    @foreach ($repartidores as $repartidor)
                        {{-- Preseleccionar old() o el valor actual del pedido --}}
                        <option value="{{ $repartidor->id }}" {{ old('id_repartidor', $pedido->id_repartidor) == $repartidor->id ? 'selected' : '' }}>
                            {{ $repartidor->nombre }}
                        </option>
                    @endforeach
                </select>
                @error('id_repartidor')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            @endisset
             {{-- Fin Selector de Repartidor --}}

        </div>
    </div>

    <a href="{{ route('pedidos.index') }}" class="btn btn-secondary">Cancelar</a>
    <button type="submit" class="btn btn-primary">Actualizar Pedido</button>
</form>

{{-- Plantilla para nuevas filas de producto (oculta) --}}
<template id="producto-row-template">
    <div class="row align-items-end mb-3 producto-item" data-index="__INDEX__">
        <div class="col-md-6">
            <label class="form-label">Producto</label>
            {{-- Usar $productosDisponibles para la lista completa en la plantilla --}}
            <select name="productos[__INDEX__][id]" class="form-select producto-select">
                <option value="" selected>Seleccione un producto...</option>
                 @isset($productosDisponibles)
                    @foreach ($productosDisponibles as $producto)
                        <option value="{{ $producto->id }}" data-precio="{{ $producto->precio }}">
                            {{ $producto->nombre }} ({{ number_format($producto->precio, 2) }}€)
                        </option>
                    @endforeach
                 @endisset
            </select>
            <div class="invalid-feedback d-block validation-placeholder-id"></div>
        </div>
        <div class="col-md-3">
            <label class="form-label">Cantidad</label>
            <input type="number" name="productos[__INDEX__][cantidad]" class="form-control producto-cantidad" min="1" value="1" required>
             <div class="invalid-feedback d-block validation-placeholder-cantidad"></div>
        </div>
        <div class="col-md-2">
            <label class="form-label">Subtotal</label>
            <input type="text" class="form-control producto-precio-total" readonly disabled value=" 0.00€">
        </div>
        <div class="col-md-1">
            <button type="button" class="btn btn-danger btn-sm remove-producto-btn" title="Quitar producto">X</button>
        </div>
    </div>
</template>

@endsection

@push('scripts')
{{-- El script JS es el mismo que en create, funciona igual para editar --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('edit-pedido-form'); // ID diferente si quieres ser específico
    const productosSeleccionadosContainer = document.getElementById('productos-seleccionados');
    const addProductoBtn = document.getElementById('add-producto-btn');
    const productoRowTemplate = document.getElementById('producto-row-template');
    const totalPedidoSpan = document.getElementById('total-pedido');
    let productoIndex = productosSeleccionadosContainer.querySelectorAll('.producto-item').length;

    function addProductoRow() {
        if (!productoRowTemplate) return;
        const templateContent = productoRowTemplate.content.cloneNode(true);
        const rowElement = templateContent.firstElementChild;
        if (rowElement) {
            rowElement.setAttribute('data-index', productoIndex);
            rowElement.querySelectorAll('[name]').forEach(input => {
                if(input.name) { input.name = input.name.replace('__INDEX__', productoIndex); }
            });
            productosSeleccionadosContainer.appendChild(rowElement);
            addEventListenersToRow(rowElement);
            updateRowPrice(rowElement);
            productoIndex++;
            updateTotalPedido();
        } else { console.error("Error: No se pudo obtener el elemento de la plantilla."); }
    }

    function removeProductoRow(button) {
        const row = button.closest('.producto-item');
        if (row) { row.remove(); updateTotalPedido(); }
    }

    function updateRowPrice(row) {
        const select = row.querySelector('.producto-select');
        const cantidadInput = row.querySelector('.producto-cantidad');
        const precioTotalInput = row.querySelector('.producto-precio-total');
        if (!select || !cantidadInput || !precioTotalInput) return;
        const selectedOption = select.options[select.selectedIndex];
        const precioUnitario = parseFloat(selectedOption?.getAttribute('data-precio') || 0);
        const cantidad = parseInt(cantidadInput.value) || 0;
        const precioTotalFila = (cantidad >= 1 && precioUnitario > 0) ? precioUnitario * cantidad : 0;
        precioTotalInput.value = new Intl.NumberFormat('es-PE', { style: 'currency', currency: 'PEN' }).format(precioTotalFila);
        updateTotalPedido();
    }

    function updateTotalPedido() {
        let totalGeneral = 0;
        productosSeleccionadosContainer.querySelectorAll('.producto-item').forEach(row => {
             const select = row.querySelector('.producto-select');
             const cantidadInput = row.querySelector('.producto-cantidad');
             if (!select || !cantidadInput) return;
             const selectedOption = select.options[select.selectedIndex];
             const precioUnitario = parseFloat(selectedOption?.getAttribute('data-precio') || 0);
             const cantidad = parseInt(cantidadInput.value) || 0;
             if (select.value && cantidad >= 1 && precioUnitario > 0) { totalGeneral += precioUnitario * cantidad; }
        });
        if(totalPedidoSpan) { totalPedidoSpan.textContent = new Intl.NumberFormat('es-PE', { style: 'currency', currency: 'PEN' }).format(totalGeneral); }
    }

     function addEventListenersToRow(row) {
        const removeBtn = row.querySelector('.remove-producto-btn');
        const select = row.querySelector('.producto-select');
        const cantidadInput = row.querySelector('.producto-cantidad');
        if (removeBtn) { removeBtn.addEventListener('click', function() { removeProductoRow(this); }); }
         if (select) { select.addEventListener('change', function() { updateRowPrice(row); clearValidationError(row, '.validation-placeholder-id'); select.classList.remove('is-invalid'); }); }
         if (cantidadInput) { cantidadInput.addEventListener('input', function() { updateRowPrice(row); clearValidationError(row, '.validation-placeholder-cantidad'); cantidadInput.classList.remove('is-invalid'); }); }
    }

    function clearValidationError(row, placeholderSelector) {
        const errorPlaceholder = row.querySelector(placeholderSelector);
        if (errorPlaceholder) { errorPlaceholder.textContent = ''; }
    }

    function validateFormClientSide() {
        let isValid = true;
        productosSeleccionadosContainer.querySelectorAll('.producto-item').forEach(row => {
            const select = row.querySelector('.producto-select');
            const cantidadInput = row.querySelector('.producto-cantidad');
            if (!select.value) { isValid = false; select.classList.add('is-invalid'); const ph = row.querySelector('.validation-placeholder-id'); if(ph) ph.textContent='Selecciona un producto.'; }
            else { select.classList.remove('is-invalid'); clearValidationError(row, '.validation-placeholder-id'); }
            const cantidad = parseInt(cantidadInput.value);
            if (isNaN(cantidad) || cantidad < 1) { isValid = false; cantidadInput.classList.add('is-invalid'); }
            else { cantidadInput.classList.remove('is-invalid'); clearValidationError(row, '.validation-placeholder-cantidad'); }
        });
        if(productosSeleccionadosContainer.querySelectorAll('.producto-item').length === 0) { isValid = false; alert("Debes añadir al menos un producto al pedido."); }
        return isValid;
    }

    // Inicialización
    if (addProductoBtn) { addProductoBtn.addEventListener('click', addProductoRow); }
    productosSeleccionadosContainer.querySelectorAll('.producto-item').forEach(row => { addEventListenersToRow(row); updateRowPrice(row); }); // Calcula subtotales iniciales
    updateTotalPedido(); // Calcula total inicial
    if (form) { form.addEventListener('submit', function(event) { if (!validateFormClientSide()) { event.preventDefault(); alert('Por favor, revisa los campos marcados en rojo en la sección de productos.'); } }); }
});
</script>
@endpush