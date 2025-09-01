<?php

namespace Database\Seeders;

use App\Models\Usuario;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ChatUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create test users for chat functionality
        Usuario::create([
            'nombre' => 'Juan Pérez',
            'email' => 'juan@example.com',
            'password' => Hash::make('password'),
            'is_online' => false,
            'last_seen' => now()->subMinutes(5)
        ]);

        Usuario::create([
            'nombre' => 'María García',
            'email' => 'maria@example.com',
            'password' => Hash::make('password'),
            'is_online' => true,
            'last_seen' => now()
        ]);

        Usuario::create([
            'nombre' => 'Carlos López',
            'email' => 'carlos@example.com',
            'password' => Hash::make('password'),
            'is_online' => false,
            'last_seen' => now()->subHours(2)
        ]);
    }
}
