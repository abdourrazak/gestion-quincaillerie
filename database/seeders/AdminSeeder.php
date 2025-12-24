<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Créer un utilisateur administrateur par défaut
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@quincaillerie.com'],
            [
                'name' => 'Administrateur',
                'password' => bcrypt('Admin@2025'),
                'role' => 'admin',
                'actif' => true,
            ]
        );

        $this->command->info('✅ Utilisateur admin créé avec succès !');
        $this->command->info('📧 Email: admin@quincaillerie.com');
        $this->command->info('🔑 Mot de passe: Admin@2025');
    }
}
