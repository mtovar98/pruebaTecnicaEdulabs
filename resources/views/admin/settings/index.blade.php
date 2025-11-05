<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Configuración de Almacenamiento</title>
  @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-100">
  <div class="max-w-3xl mx-auto p-6 space-y-6">
    <h1 class="text-2xl font-bold">Configuración de Almacenamiento</h1>

    @if(session('ok'))
      <div class="p-3 bg-green-100 text-green-800 rounded">{{ session('ok') }}</div>
    @endif

    {{-- Cuota Global --}}
    <div class="bg-white p-4 rounded shadow space-y-3">
      <h2 class="font-semibold">Cuota Global</h2>
      <form method="POST" action="{{ route('admin.settings.global.update') }}" class="flex items-center gap-3">
        @csrf
        <label class="text-sm">Default (MB):</label>
        <input name="default_quota_mb" type="number" min="1" max="20480" required
               value="{{ old('default_quota_mb', optional($global)->default_quota_mb ?? 10) }}"
               class="border rounded px-3 py-2 w-32">
        <button class="px-4 py-2 bg-blue-600 text-white rounded">Guardar</button>
      </form>
      @error('default_quota_mb')
        <p class="text-red-600 text-sm">{{ $message }}</p>
      @enderror
    </div>

    {{-- Extensiones Prohibidas --}}
    <div class="bg-white p-4 rounded shadow space-y-3">
      <h2 class="font-semibold">Extensiones Prohibidas</h2>

      <form method="POST" action="{{ route('admin.settings.banned.add') }}" class="flex items-center gap-3">
        @csrf
        <input name="extension" type="text" placeholder="ej. exe" class="border rounded px-3 py-2 w-40">
        <button class="px-4 py-2 bg-blue-600 text-white rounded">Agregar</button>
      </form>
      @error('extension')
        <p class="text-red-600 text-sm">{{ $message }}</p>
      @enderror

      <ul class="list-disc ml-6">
        @forelse($banned as $ext)
          <li class="flex items-center justify-between">
            <span>.{{ $ext->extension }}</span>
            <form method="POST" action="{{ route('admin.settings.banned.delete', $ext) }}">
              @csrf @method('DELETE')
              <button class="text-red-600 hover:underline">Eliminar</button>
            </form>
          </li>
        @empty
          <li class="text-gray-600"><em>No hay extensiones prohibidas</em></li>
        @endforelse
      </ul>
    </div>

    <div>
      <a href="{{ route('admin.dashboard') }}" class="text-blue-600 underline">Volver al panel</a>
    </div>
  </div>
</body>
</html>
