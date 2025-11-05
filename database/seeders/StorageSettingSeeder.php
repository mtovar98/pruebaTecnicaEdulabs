<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\StorageGlobalSetting;
use App\Models\BannedExtension;

class StorageSettingSeeder extends Seeder
{
    public function run(): void
    {
        // Cuota global por defecto = 10 MB (si no existe, créala)
        StorageGlobalSetting::query()->firstOrCreate([], [
            'default_quota_mb' => 10,
        ]);

        // Lista base de extensiones prohibidas
        $banned = ['exe', 'bat', 'js', 'php', 'sh'];
        foreach ($banned as $ext) {
            BannedExtension::firstOrCreate(['extension' => $ext]);
        }
    }
}
