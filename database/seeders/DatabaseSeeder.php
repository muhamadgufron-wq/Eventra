<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        User::factory()->create([
            'name'  => 'Owner WO',
            'email' => 'owner@womanager.test',
            'role'  => 'owner',
        ]);

        User::factory()->create([
            'name'  => 'Admin WO',
            'email' => 'admin@womanager.test',
            'role'  => 'admin',
        ]);

        User::factory()->create([
            'name'  => 'Staff WO',
            'email' => 'staff@womanager.test',
            'role'  => 'staff',
        ]);
    }
}
