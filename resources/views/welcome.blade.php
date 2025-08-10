<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Social Hub Manager</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-800 antialiased">
    <div class="min-h-screen flex flex-col justify-center items-center px-6 py-8">
        
        <!-- Logo o icono -->
        <div class="mb-6">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-16 h-16 text-indigo-600" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10 2a8 8 0 100 16 8 8 0 000-16zM8.5 6.5l4.5 3-4.5 3v-6z" />
            </svg>
        </div>

        <!-- Título y descripción -->
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Social Hub Manager</h1>
        <p class="text-gray-600 text-center max-w-lg">
            Gestiona todas tus redes sociales desde un solo lugar. Publica contenido, analiza estadísticas y conecta con tu audiencia de manera eficiente.
        </p>

        <!-- Botones -->
        <div class="mt-8 flex flex-col sm:flex-row gap-4 w-full sm:w-auto">
            <a href="{{ route('login') }}" 
               class="px-6 py-3 bg-indigo-600 text-white text-sm font-medium rounded-lg shadow hover:bg-indigo-700 transition">
                Iniciar Sesión
            </a>
            @if (Route::has('register'))
            <a href="{{ route('register') }}" 
               class="px-6 py-3 bg-white border border-gray-300 text-sm font-medium rounded-lg shadow hover:bg-gray-50 transition">
                Registrarse
            </a>
            @endif
        </div>

        <!-- Imagen ilustrativa -->
        <div class="mt-10">
            <img src="https://source.unsplash.com/600x300/?social,media" alt="Social Media" class="rounded-lg shadow-lg max-w-full">
        </div>

    </div>
</body>
</html>
