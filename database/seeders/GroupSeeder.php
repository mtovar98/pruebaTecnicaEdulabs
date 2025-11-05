<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Group;

class GroupSeeder extends Seeder
{
    public function run(): void
    {
        Group::firstOrCreate(['name' => 'Marketing'],   ['description' => 'Equipo de marketing']);
        Group::firstOrCreate(['name' => 'Developers'],  ['description' => 'Equipo de desarrollo']);
    }
}
