<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Mis Archivos</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-100">
  <div class="max-w-3xl mx-auto p-6 space-y-6">
    <h1 class="text-2xl font-bold">Mis Archivos</h1>
    <div class="bg-white p-4 rounded shadow">
        <h2 class="font-semibold mb-2">Uso de almacenamiento</h2>
        <p class="text-gray-700">
            Usado: <strong>{{ $labels['used'] }}</strong>
            &nbsp;•&nbsp;
            Cuota: <strong>{{ $labels['assigned'] }}</strong>
            &nbsp;•&nbsp;
            Disponible: <strong>{{ $labels['remaining'] }}</strong>
        </p>

        <div class="mt-3 w-full bg-gray-200 rounded border border-black h-3 overflow-hidden">
            <div class="bg-green-800 h-3" style="width: {{ $labels['percent'] }}%; background: green;"></div>
        </div>
        <p class="mt-1 text-xs text-gray-600">{{ $labels['percent'] }}%</p>
    </div>


    <div class="bg-white p-4 rounded shadow">
      <form id="uploadForm" class="space-y-3">
        @csrf
        <input id="fileInput" type="file" name="file" class="block w-full" required />
        <button id="btnUpload" type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">
          Subir
        </button>
      </form>

      <div id="msg" class="mt-3 text-sm"></div>
    </div>

    <div class="bg-white p-4 rounded shadow">
      <h2 class="font-semibold mb-2">Archivos subidos</h2>
      @if($items->isEmpty())
        <p class="text-gray-600"><em>No tienes archivos todavía.</em></p>
      @else
        <ul id="fileList" class="list-disc ml-6">
          @foreach($items as $it)
            <li class="flex items-center justify-between gap-3">
                <span>
                {{ $it->original_name }} — {{ number_format($it->size_bytes) }} bytes
                <span class="text-gray-500">({{ $it->extension ?: 'sin ext' }})</span>
                </span>
                <span class="flex items-center gap-2">
                <a class="text-blue-600 underline"
                    href="{{ route('files.download', $it->id) }}">Descargar</a>

                <form method="POST" action="{{ route('files.destroy', $it->id) }}"
                        onsubmit="return confirm('¿Eliminar este archivo?');">
                    @csrf @method('DELETE')
                    <button class="text-red-600 hover:underline">Eliminar</button>
                </form>
                </span>
            </li>
            @endforeach

        </ul>
      @endif
    </div>

    <div>
      <a href="{{ route('dashboard') }}" class="text-blue-600 underline">Volver al dashboard</a>
    </div>
  </div>

 <script>
  const form = document.getElementById('uploadForm');
  const input = document.getElementById('fileInput');
  const msg = document.getElementById('msg');
  const list = document.getElementById('fileList') || (() => {
    const ul = document.createElement('ul');
    ul.id = 'fileList';
    ul.className = 'list-disc ml-6';
    form.parentElement.parentElement.nextElementSibling.appendChild(ul);
    return ul;
  })();

  // NUEVO: helpers
  const fmtBytes = (n) => {
    if (n === 0) return '0 B';
    const k = 1024, units = ['B','KB','MB','GB','TB'];
    const i = Math.floor(Math.log(n) / Math.log(k));
    return (n / Math.pow(k, i)).toFixed(i ? 2 : 0) + ' ' + units[i];
  };

  const flash = (text, ok = true) => {
    msg.textContent = text;
    msg.className = 'mt-3 text-sm ' + (ok ? 'text-green-600' : 'text-red-600');
  };

  // NUEVO: actualizar barra/labels sin recargar
  const usageBar = document.querySelector('.bg-blue-600.h-3');
  const percentText = document.querySelector('p.text-xs');
  const usageLine = document.querySelector('div.bg-white p.text-gray-700'); // “Usado: … • Cuota: …”
  const parseMB = (s) => parseFloat((s.match(/([\d.]+)\s*MB/)||[])[1] || '0');

  const updateUsage = (addedBytes) => {
    if (!usageLine || !usageBar || !percentText) return;
    const m = usageLine.innerText.match(/Usado:\s*(\d+(\.\d+)?) MB.*Cuota:\s*(\d+(\.\d+)?) MB/i);
    if (!m) return;
    let used = parseFloat(m[1]);
    const quota = parseFloat(m[3]);

    used = used + (addedBytes / (1024*1024));
    const remaining = Math.max(0, quota - used);
    const percent = Math.min(100, Math.round((used / quota) * 100));

    usageLine.innerHTML = `Usado: <strong>${used.toFixed(2)} MB</strong> • Cuota: <strong>${quota.toFixed(2)} MB</strong> • Disponible: <strong>${remaining.toFixed(2)} MB</strong>`;
    usageBar.style.width = percent + '%';
    percentText.textContent = percent + '%';
  };

  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    flash('');
    const file = input.files[0];
    if (!file) return flash('Selecciona un archivo.', false);

    const fd = new FormData();
    fd.append('file', file);

    try {
      const res = await fetch('{{ route('files.upload') }}', {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        },
        body: fd,
        credentials: 'same-origin',
      });

      let data = {};
      try { data = await res.json(); } catch { /* ignore */ }

      if (!res.ok || !data.ok) {
        // Mostrar mensaje específico si viene del backend
        if (data && data.message) return flash(data.message, false);
        return flash('Error al subir.', false);
      }

      flash('Archivo subido correctamente.', true);

      // Agregar a la lista
        const li = document.createElement('li');
        li.className = 'flex items-center justify-between gap-3';

        const left = document.createElement('span');
        const ext = file.name.includes('.') ? file.name.split('.').pop() : 'sin ext';
        left.textContent = `${file.name} — ${fmtBytes(file.size)} (${ext})`;

        const right = document.createElement('span');
        right.className = 'flex items-center gap-2';

        // Link descargar (usamos data.id que devuelve el backend)
        const a = document.createElement('a');
        a.className = 'text-blue-600 underline';
        a.href = `{{ url('/files') }}/${data.id}/download`;
        a.textContent = 'Descargar';

        // Botón eliminar (por fetch)
        const del = document.createElement('button');
        del.className = 'text-red-600 hover:underline';
        del.textContent = 'Eliminar';
        del.addEventListener('click', async () => {
        if (!confirm('¿Eliminar este archivo?')) return;
        const resDel = await fetch(`{{ url('/files') }}/${data.id}`, {
            method: 'DELETE',
            headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
            },
            credentials: 'same-origin',
        });
        const j = await resDel.json().catch(() => ({}));
        if (resDel.ok && j.ok) {
            li.remove();
            // Actualiza barra/labels restando tamaño
            updateUsage(-file.size);
            flash('Archivo eliminado.', true);
        } else {
            flash(j.message || 'No se pudo eliminar.', false);
        }
        });

        right.appendChild(a);
        right.appendChild(del);

        li.appendChild(left);
        li.appendChild(right);
        list.prepend(li);

        });
</script>

</body>
</html>
