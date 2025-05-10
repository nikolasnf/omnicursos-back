<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@omnicursos.com.br'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('admin123'),
                'role' => 'admin'
            ]
        );

        // Cuidado: esse comando pode tentar criar usuários duplicados em múltiplos seeds
        // Verifique se você quer mesmo manter essa linha
        User::factory()->count(10)->create();
    }
}
