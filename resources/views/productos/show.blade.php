<!-- resources/views/productos/show.blade.php -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles del Producto</title>
</head>
<body>
    <h1>Detalles del Producto</h1>

    <p><strong>ID:</strong> {{ $producto->id }}</p>
    <p><strong>Nombre:</strong> {{ $producto->nombre }}</p>
    <p><strong>Precio:</strong> {{ $producto->precio }}</p>
    <p><strong>Tipo:</strong> {{ $producto->tipo }}</p>

    <br>
    <a href="{{ route('productos.index') }}">Volver a la lista de productos</a>
</body>
</html>
