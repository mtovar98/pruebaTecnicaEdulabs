<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\BannedExtension;
use App\Models\FileItem;
use App\Services\StorageQuotaService;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;


class FileUploadController extends Controller
{
    public function __construct(private StorageQuotaService $quota) {}

    public function store(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file'],
        ]);

        $user = $request->user();
        $uploaded = $request->file('file');

        // 1) Rechazar por extensión prohibida (externa)
        $ext = strtolower($uploaded->getClientOriginalExtension() ?: '');
        if ($ext && BannedExtension::where('extension', $ext)->exists()) {
            return response()->json([
                'ok' => false,
                'message' => "Error: El tipo de archivo '.{$ext}' no está permitido.",
            ], 422);
        }

        /**
         * 1.1) Si es ZIP, inspecciona el contenido y rechaza si adentro hay prohibidos
         */
        if ($ext === 'zip') {
            $tmpPath = $uploaded->getRealPath(); // archivo temporal
            $zip = new \ZipArchive();
            $openResult = $zip->open($tmpPath);

            if ($openResult === true) {
                for ($i = 0; $i < $zip->numFiles; $i++) {
                    $stat = $zip->statIndex($i);
                    if (!$stat) {
                        continue;
                    }
                    $innerName = $stat['name']; // e.g. folder/script.js
                    // tomar la extensión del archivo interno
                    $innerExt = strtolower(pathinfo($innerName, PATHINFO_EXTENSION));

                    if ($innerExt !== '') {
                        $isBanned = \App\Models\BannedExtension::where('extension', $innerExt)->exists();
                        if ($isBanned) {
                            $zip->close();
                            return response()->json([
                                'ok' => false,
                                'message' => "Error: El archivo '{$innerName}' dentro del .zip no está permitido.",
                            ], 422);
                        }
                    }
                }
                $zip->close();
            } else {
                // Si no se puede abrir, mejor rechazar por seguridad
                return response()->json([
                    'ok' => false,
                    'message' => "Error: No se pudo analizar el archivo .zip.",
                ], 422);
            }
        }


        // 2) Validar CUOTA: (uso_actual + tamaño_nuevo) > cuota_asignada ?
        $assigned = $this->quota->assignedQuotaBytes($user);
        $used = $this->quota->currentUsageBytes($user);
        $newSize = (int) $uploaded->getSize();

        if ($used + $newSize > $assigned) {
            $assignedMb = round($assigned / (1024 * 1024));
            return response()->json([
                'ok' => false,
                'message' => "Error: Cuota de almacenamiento ({$assignedMb} MB) excedida.",
            ], 422);
        }

        // 3) Guardar en /public/uploads/{user_id}/
        $dir = "uploads/{$user->id}";
        $storedPath = $uploaded->store($dir, 'public');

        // 4) Metadatos
        $size = $newSize;
        $mime = $uploaded->getMimeType();
        $original = $uploaded->getClientOriginalName();
        $checksum = hash_file('sha256', Storage::disk('public')->path($storedPath));

        // 5) Persistir
        $item = FileItem::create([
            'user_id'       => $user->id,
            'original_name' => $original,
            'stored_path'   => $storedPath,
            'size_bytes'    => $size,
            'mime_type'     => $mime,
            'extension'     => $ext,
            'checksum'      => $checksum,
        ]);

        return response()->json([
            'ok' => true,
            'id' => $item->id,
            'message' => 'Archivo subido correctamente.',
            'path' => $storedPath,
        ]);
    }

    public function index(StorageQuotaService $quota)
    {
        $user  = auth()->user();
        $items = \App\Models\FileItem::where('user_id', $user->id)
            ->latest('id')
            ->get();

        $assigned = $quota->assignedQuotaBytes($user);
        $used     = $quota->currentUsageBytes($user);
        $remaining = max(0, $assigned - $used);

        // % de uso (cap a 100)
        $percent = $assigned > 0 ? min(100, round(($used / $assigned) * 100)) : 0;

        // etiquetas amigables (MB con 2 decimales)
        $toMb = fn ($bytes) => number_format($bytes / (1024*1024), 2);

        $labels = [
            'assigned'  => $toMb($assigned) . ' MB',
            'used'      => $toMb($used) . ' MB',
            'remaining' => $toMb($remaining) . ' MB',
            'percent'   => $percent,
        ];

        return view('files.index', compact('items', 'labels'));
    }

    public function download(Request $request, FileItem $fileItem): BinaryFileResponse
    {
        // Autoriza: solo dueño
        if ($fileItem->user_id !== $request->user()->id) {
            abort(403, 'No autorizado');
        }

        $absPath = Storage::disk('public')->path($fileItem->stored_path);

        return response()->download($absPath, $fileItem->original_name);
    }

    public function destroy(Request $request, FileItem $fileItem)
    {
        // Autoriza: solo dueño
        if ($fileItem->user_id !== $request->user()->id) {
            abort(403, 'No autorizado');
        }

        // Borra archivo físico (ignora si ya no existe)
        \Illuminate\Support\Facades\Storage::disk('public')->delete($fileItem->stored_path);

        // Borra metadata
        $fileItem->delete();

        // Si quieres, devuelve JSON si la petición es fetch
        if ($request->wantsJson()) {
            return response()->json(['ok' => true, 'message' => 'Archivo eliminado.']);
        }

        // O redirige de vuelta
        return redirect()->route('files.index')->with('ok', 'Archivo eliminado.');
    }
}

