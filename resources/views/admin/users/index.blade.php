<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Usuarios y Grupos</title>
  @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-100">
  <div class="max-w-5xl mx-auto p-6 space-y-6">
    <h1 class="text-2xl font-bold">Usuarios y sus grupos</h1>

    <div class="bg-white rounded shadow overflow-hidden">
      <table class="w-full text-center">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-4 py-2">Nombre</th>
            <th class="px-4 py-2">Email</th>
            <th class="px-4 py-2">Grupos</th>
          </tr>
        </thead>
        <tbody>
          @forelse($users as $u)
            <tr class="border-t align-top">
                <td class="px-4 py-2">{{ $u->name }}</td>
                <td class="px-4 py-2">{{ $u->email }}</td>
                <td class="px-4 py-2 text-center">
                    {{-- listado actual con botón quitar --}}
                    @if($u->groups->isEmpty())
                    <span class="text-gray-500">—</span>
                    @else
                    <ul class="flex flex-wrap gap-2 justify-center">
                        @foreach($u->groups as $g)
                        <li class="inline-flex items-center gap-4 bg-gray-100 px-2 py-1 rounded">
                            <span>{{ $g->name }}</span>
                            <form method="POST" action="{{ route('admin.users.removeGroup', [$u->id, $g->id]) }}">
                            @csrf @method('DELETE')
                            <button class="text-red-600 text-xs hover:underline" title="Quitar">x</button>
                            </form>
                        </li>
                        @endforeach
                    </ul>
                    @endif

                    {{-- asignar nuevo --}}
                    <form method="POST" action="{{ route('admin.users.assignGroup', $u->id) }}" class="mt-2 flex gap-2 justify-center">
                    @csrf
                    <select name="group_id" class="border rounded px-8 py-1">
                        @foreach($groups as $g)
                        <option value="{{ $g->id }}">{{ $g->name }}</option>
                        @endforeach
                    </select>
                    <button class="px-2 py-1 bg-blue-600 text-white rounded text-sm">Agregar</button>
                    </form>
                </td>
            </tr>

          @empty
            <tr><td class="px-4 py-4" colspan="3"><em>No hay usuarios</em></td></tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="flex gap-4">
      <a href="{{ route('admin.settings.index') }}" class="text-blue-600 underline">← Configuración</a>
      <a href="{{ route('admin.dashboard') }}" class="text-blue-600 underline">Panel</a>
    </div>
  </div>
</body>
</html>
