<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Cuotas por Usuario</title>
  @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-100">
  <div class="max-w-4xl mx-auto p-6 space-y-6">
    <h1 class="text-2xl font-bold">Cuotas por Usuario</h1>

    @if(session('ok'))
      <div class="p-3 bg-green-100 text-green-800 rounded">{{ session('ok') }}</div>
    @endif

    <div class="bg-white p-4 rounded shadow space-y-3">
      <h2 class="font-semibold">Asignar / Actualizar cuota</h2>
      <form method="POST" action="{{ route('admin.settings.user.save') }}" class="grid grid-cols-1 md:grid-cols-3 gap-3">
        @csrf
        <div>
          <label class="text-sm">Usuario</label>
          <select name="user_id" class="border rounded px-3 py-2 w-full" required>
            @foreach($users as $u)
              <option value="{{ $u->id }}">{{ $u->name }} — {{ $u->email }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="text-sm">Cuota (MB)</label>
          <input name="quota_mb" type="number" min="1" max="20480" class="border rounded px-3 py-2 w-full" placeholder="(vacío = hereda)">
        </div>
        <div class="flex items-end">
          <button class="px-4 py-2 bg-blue-600 text-white rounded w-full">Guardar</button>
        </div>
      </form>
      @error('user_id') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
      @error('quota_mb') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
    </div>

    <div class="bg-white p-4 rounded shadow space-y-2">
      <h2 class="font-semibold">Usuarios con cuota específica</h2>
      @forelse($limits as $lim)
        <div class="flex items-center justify-between border-b last:border-0 py-2">
          <div>
            <strong>{{ $lim->user->name }}</strong>
            <span class="text-gray-600">— {{ $lim->user->email }}</span>
            <span class="text-gray-600"> — Cuota: {{ $lim->quota_mb ? $lim->quota_mb.' MB' : 'Hereda' }}</span>
          </div>
          <form method="POST" action="{{ route('admin.settings.user.delete', $lim) }}">
            @csrf @method('DELETE')
            <button class="text-red-600 hover:underline">Eliminar cuota</button>
          </form>
        </div>
      @empty
        <p class="text-gray-600"><em>No hay usuarios con cuota específica.</em></p>
      @endforelse
    </div>

    <div class="flex gap-4">
      <a href="{{ route('admin.settings.index') }}" class="text-blue-600 underline">← Configuración global</a>
      <a href="{{ route('admin.settings.group.limits') }}" class="text-blue-600 underline">Cuotas por grupo →</a>
    </div>
  </div>
</body>
</html>
