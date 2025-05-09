<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}"> {{-- Para JS si haces llamadas AJAX --}}

    <title>@yield('title', 'Gestión de Pedidos') - {{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Estilos (Ejemplo con Bootstrap CDN) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    {{-- <link rel="stylesheet" href="{{ asset('css/app.css') }}"> --}} {{-- Si usas compilación de assets --}}
    @stack('styles') {{-- Para estilos específicos de cada página --}}

    <!-- Scripts -->
    {{-- @vite(['resources/css/app.css', 'resources/js/app.js']) --}} {{-- Si usas Vite --}}
</head>
<body class="font-sans antialiased">
    <div class="min-vh-100 bg-light">
        {{-- Aquí podrías incluir una barra de navegación si la tienes --}}
        {{-- @include('layouts.navigation') --}}
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
            <div class="container">
                <a class="navbar-brand" href="{{ route('pedidos.index') }}">Gestión Pedidos</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('pedidos.*') ? 'active' : '' }}" href="{{ route('pedidos.index') }}">Pedidos</a>
                        </li>
                         <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('productos.*') ? 'active' : '' }}" href="{{ route('productos.index') }}">Productos</a>
                        </li>
                        {{-- Añade más enlaces si es necesario --}}
                    </ul>
                </div>
            </div>
        </nav>


        <!-- Page Heading -->
        @hasSection('header')
            <header class="bg-white shadow-sm">
                <div class="container py-3">
                    @yield('header')
                </div>
            </header>
        @endif

        <!-- Page Content -->
        <main class="container py-4">
            {{-- Mensajes Flash de Éxito/Error --}}
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

             {{-- Contenido principal de la página --}}
            @yield('content')
        </main>
    </div>

    <!-- Scripts JS (Ejemplo con Bootstrap Bundle CDN) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    {{-- <script src="{{ asset('js/app.js') }}"></script> --}} {{-- Si usas compilación de assets --}}
    @stack('scripts') {{-- Para scripts específicos de cada página --}}
</body>
</html>