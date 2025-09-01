<?php

namespace Database\Seeders;

use App\Models\Usuario;
use App\Models\CategoriasDeServicio;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Crear categorías de servicio
        $categorias = [
            ['nombre_categoria' => 'Tecnología', 'descripcion_categoria' => 'Servicios relacionados con tecnología'],
            ['nombre_categoria' => 'Educación', 'descripcion_categoria' => 'Servicios educativos'],
            ['nombre_categoria' => 'Negocios', 'descripcion_categoria' => 'Servicios de negocios'],
            ['nombre_categoria' => 'Legal', 'descripcion_categoria' => 'Servicios legales'],
        ];

        foreach ($categorias as $categoria) {
            CategoriasDeServicio::firstOrCreate(['nombre_categoria' => $categoria['nombre_categoria']], $categoria);
        }

        // Crear usuarios de prueba
        $usuarios = [
            [
                'nombre' => 'Juan Pérez',
                'email' => 'juan@test.com',
                'password' => Hash::make('password123'),
            ],
            [
                'nombre' => 'María García',
                'email' => 'maria@test.com',
                'password' => Hash::make('password123'),
            ],
            [
                'nombre' => 'Carlos López',
                'email' => 'carlos@test.com',
                'password' => Hash::make('password123'),
            ]
        ];

        foreach ($usuarios as $usuario) {
            Usuario::firstOrCreate(['email' => $usuario['email']], $usuario);
        }

        // Crear conversaciones de prueba
        $juan = Usuario::where('email', 'juan@test.com')->first();
        $maria = Usuario::where('email', 'maria@test.com')->first();
        $carlos = Usuario::where('email', 'carlos@test.com')->first();

        if ($juan && $maria) {
            // Crear conversación entre Juan y María
            $conversation1 = \App\Models\Conversation::firstOrCreate([
                'user_one' => $juan->id_usuario,
                'user_two' => $maria->id_usuario,
            ], [
                'last_message_at' => now()
            ]);

            // Crear algunos mensajes de prueba
            \App\Models\Message::create([
                'conversation_id' => $conversation1->id,
                'sender_id' => $juan->id_usuario,
                'message' => '¡Hola María! ¿Cómo estás?'
            ]);

            \App\Models\Message::create([
                'conversation_id' => $conversation1->id,
                'sender_id' => $maria->id_usuario,
                'message' => '¡Hola Juan! Todo bien, ¿y tú?'
            ]);

            \App\Models\Message::create([
                'conversation_id' => $conversation1->id,
                'sender_id' => $juan->id_usuario,
                'message' => 'Perfecto, trabajando en el proyecto de chat.'
            ]);
        }

        if ($juan && $carlos) {
            // Crear conversación entre Juan y Carlos
            $conversation2 = \App\Models\Conversation::firstOrCreate([
                'user_one' => $juan->id_usuario,
                'user_two' => $carlos->id_usuario,
            ], [
                'last_message_at' => now()
            ]);

            // Crear algunos mensajes de prueba
            \App\Models\Message::create([
                'conversation_id' => $conversation2->id,
                'sender_id' => $carlos->id_usuario,
                'message' => '¿Viste la nueva funcionalidad del chat?'
            ]);

            \App\Models\Message::create([
                'conversation_id' => $conversation2->id,
                'sender_id' => $juan->id_usuario,
                'message' => 'Sí, está genial. El cifrado funciona perfecto.'
            ]);
        }
    }
}
