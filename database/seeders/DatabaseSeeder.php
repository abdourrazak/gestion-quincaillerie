<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            AdminSeeder::class,
            CategorieSeeder::class,
            FournisseurSeeder::class,
            ProduitSeeder::class,
            ClientSeeder::class,
        ]);
        
        $this->command->info('');
        $this->command->info('🎉 Base de données remplie avec succès !');
        $this->command->info('');
    }
}
