@extends('layouts.app')

@section('title', 'Editar Pedido #' . $pedido->id)

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3">Editar Pedido #{{ $pedido->id }}</h1>
        <a href="{{ route('pedidos.index') }}" class="btn btn-outline-secondary btn-sm">
            ← Volver a la lista
        </a>
    </div>

    {{-- Errores generales --}}
    @if ($errors->has('error_general'))
        <div class="alert alert-danger">{{ $errors->first('error_general') }}</div>
    @endif

    <form action="{{ route('pedidos.update', $pedido) }}" method="POST" id="edit-pedido-form">
        @csrf
        @method('PUT')

        {{-- Sección Productos --}}
        <div class="card mb-4">
            <div class="card-header bg-light">
                <strong>Productos del Pedido</strong>
            </div>
            <div class="card-body">
                <div id="productos-seleccionados">
                    @php
                        $productosEnFormulario = old('productos', $productosActuales->map(fn($cantidad, $id) => ['id' => $id, 'cantidad' => $cantidad])->values()->all());
                    @endphp

                    @foreach($productosEnFormulario as $index => $productoData)
                        @if(isset($productoData['id'], $productoData['cantidad']))
                            <div class="row mb-3 producto-item align-items-center" data-index="{{ $index }}">
                                <div class="col-md-6">
                                    <select name="productos[{{ $index }}][id]" class="form-select @error('productos.' . $index . '.id') is-invalid @enderror">
                                        <option value="">-- Seleccione producto --</option>
                                        @foreach ($productosDisponibles as $producto)
                                            <option value="{{ $producto->id }}" data-precio="{{ $producto->precio }}"
                                                {{ $productoData['id'] == $producto->id ? 'selected' : '' }}>
                                                {{ $producto->nombre }} ({{ number_format($producto->precio,2) }}€)
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('productos.' . $index . '.id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-3">
                                    <input type="number"
                                           name="productos[{{ $index }}][cantidad]"
                                           class="form-control @error('productos.' . $index . '.cantidad') is-invalid @enderror"
                                           min="1"
                                           value="{{ $productoData['cantidad'] }}"
                                           placeholder="Cantidad">
                                    @error('productos.' . $index . '.cantidad')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-2">
                                    <input type="text"
                                           class="form-control producto-precio-total"
                                           readonly
                                           value="0.00€">
                                </div>
                                <div class="col-md-1 text-end">
                                    <button type="button" class="btn btn-outline-danger btn-sm remove-producto-btn">&times;</button>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>

                {{-- Errores relacionados --}}
                @if ($errors->has('productos') && !$errors->has('productos.*'))
                    <div class="mt-2 text-danger">{{ $errors->first('productos') }}</div>
                @endif
                @if ($errors->has('productos.*.id') || $errors->has('productos.*.cantidad'))
                    <div class="mt-2 text-danger">
                        Corrige los productos: asegúrate de seleccionar un producto y una cantidad (>=1).
                    </div>
                @endif

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
                            <input type="text"
                                   name="direccion"
                                   id="direccion"
                                   class="form-control @error('direccion') is-invalid @enderror"
                                   value="{{ old('direccion', $pedido->direccion) }}"
                                   placeholder="Dirección">
                            <label for="direccion">Dirección</label>
                            @error('direccion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating">
                            <input type="text"
                                   name="telefono"
                                   id="telefono"
                                   class="form-control @error('telefono') is-invalid @enderror"
                                   value="{{ old('telefono', $pedido->telefono) }}"
                                   placeholder="Teléfono">
                            <label for="telefono">Teléfono</label>
                            @error('telefono')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-floating">
                            <select name="id_repartidor"
                                    id="id_repartidor"
                                    class="form-select @error('id_repartidor') is-invalid @enderror">
                                <option value="">-- Asignar --</option>
                                @foreach ($repartidores as $r)
                                    <option value="{{ $r->id }}"
                                        {{ old('id_repartidor', $pedido->id_repartidor) == $r->id ? 'selected' : '' }}>
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
            <button type="submit" class="btn btn-primary">Actualizar Pedido</button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const container = document.getElementById('productos-seleccionados');
    const addBtn    = document.getElementById('add-producto-btn');
    const template  = document.getElementById('producto-row-template');
    const totalSpan = document.getElementById('total-pedido');
    let indexCount  = container.querySelectorAll('.producto-item').length;

    function recalcTotals() {
        let total = 0;
        container.querySelectorAll('.producto-item').forEach(row => {
            const sel = row.querySelector('.producto-select');
            const qty = row.querySelector('.producto-cantidad');
            const priceEl = row.querySelector('.producto-precio-total');
            const priceUnit = parseFloat(sel.selectedOptions[0]?.dataset.precio || 0);
            const quantity  = parseInt(qty.value) || 0;
            const sub = (priceUnit * quantity).toFixed(2);
            priceEl.value = sub + '€';
            total += parseFloat(sub);
        });
        totalSpan.textContent = total.toFixed(2) + '€';
    }

    function addRow() {
        const tpl = template.content.cloneNode(true);
        const row = tpl.querySelector('.producto-item');
        row.dataset.index = indexCount;
        row.querySelectorAll('[name]').forEach(el => {
            el.name = el.name.replace(/__INDEX__/, indexCount);
        });
        container.appendChild(row);
        bindRowEvents(row);
        indexCount++;
        recalcTotals();
    }

    function bindRowEvents(row) {
        row.querySelector('.remove-producto-btn').addEventListener('click', () => {
            row.remove();
            recalcTotals();
        });
        row.querySelector('.producto-select').addEventListener('change', () => recalcTotals());
        row.querySelector('.producto-cantidad').addEventListener('input', () => recalcTotals());
    }

    // Inicializar filas existentes
    container.querySelectorAll('.producto-item').forEach(bindRowEvents);
    recalcTotals();

    if (addBtn) {
        addBtn.addEventListener('click', addRow);
    }
});
</script>
@endpush
