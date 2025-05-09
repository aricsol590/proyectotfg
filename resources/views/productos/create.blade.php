<!-- resources/views/productos/create.blade.php -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Producto</title>
</head>
<body>
    <h1>Crear Producto</h1>

    <form action="{{ route('productos.store') }}" method="POST">
        @csrf
        <label for="nombre">Nombre:</label>
        <input type="text" name="nombre" id="nombre" required><br>

        <label for="precio">Precio:</label>
        <input type="number" name="precio" id="precio" required><br>

        <label for="tipo">Tipo:</label>
        <input type="text" name="tipo" id="tipo" required><br>

        <button type="submit">Guardar Producto</button>
    </form>

    <br>
    <a href="{{ route('productos.index') }}">Volver a la lista de productos</a>
</body>
</html>
