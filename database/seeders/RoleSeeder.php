<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // simple y directo (estilo junior)
        Role::firstOrCreate(['name' => 'admin'], ['label' => 'Administrador']);
        Role::firstOrCreate(['name' => 'user'],  ['label' => 'Usuario']);
    }
}
