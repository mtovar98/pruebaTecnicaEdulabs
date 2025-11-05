<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Panel Admin</title>
  @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-100">
  <div class="max-w-3xl mx-auto p-6">
    <h1 class="text-2xl font-bold mb-4">Panel de Administración</h1>
    <p class="mb-6 text-gray-700">Bienvenido. (placeholder)</p>

    <a href="{{ route('dashboard') }}" class="text-blue-600 underline">Ir al dashboard de usuario</a>
  </div>
</body>
</html>
