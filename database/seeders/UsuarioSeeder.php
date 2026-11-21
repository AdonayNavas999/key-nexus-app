<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsuarioSeeder extends Seeder
{
    /**
     * Crea el usuario principal para acceder a la aplicación Key Nexus.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'adonay@keynexus.com'],
            [
                'name'     => 'Adonay Navas',
                'email'    => 'adonay@keynexus.com',
                'password' => Hash::make('KeyNexus2026@Safe'),
            ]
        );

        $this->command->info('✅ Usuario creado correctamente.');
        $this->command->line('   ────────────────────────────────────');
        $this->command->line('   📧 Correo  : adonay@keynexus.com');
        $this->command->line('   🔑 Password: KeyNexus2026@Safe');
        $this->command->line('   ────────────────────────────────────');
    }
}
