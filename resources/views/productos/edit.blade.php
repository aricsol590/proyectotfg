<!-- resourceviewproductoedit.blade.php -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Producto</title>
</head>
<body>
    <h1>Editar Producto</h1>

    <form action="{{ route('productos.update', $producto) }}" method="POST">
        @csrf
        @method('PUT')

        <label for="nombre">Nombre:</label>
        <input type="text" name="nombre" id="nombre" value="{{ $producto->nombre }}" required><br>

        <label for="precio">Precio:</label>
        <input type="number" name="precio" id="precio" value="{{ $producto->precio }}" required><br>

        <label for="tipo">Tipo:</label>
        <input type="text" name="tipo" id="tipo" value="{{ $producto->tipo }}" required><br>

        <button type="submit">Actualizar Producto</button>
    </form>

    <br>
    <a href="{{ route('productos.index') }}">Volver a la lista de productos</a>
</body>
</html>
