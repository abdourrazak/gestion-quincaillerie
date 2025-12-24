<?php

namespace Database\Seeders;

use App\Models\Client;
use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    /**
     * Créer des clients de test
     */
    public function run(): void
    {
        $clients = [
            [
                'nom' => 'Dubois',
                'prenom' => 'Jean',
                'entreprise' => null,
                'email' => 'jean.dubois@email.fr',
                'telephone' => '06 12 34 56 78',
                'adresse' => '12 Rue de la Paix',
                'ville' => 'Paris',
                'code_postal' => '75002',
            ],
            [
                'nom' => 'Leroy',
                'prenom' => 'Marie',
                'entreprise' => 'Leroy Construction SARL',
                'email' => 'contact@leroy-construction.fr',
                'telephone' => '06 23 45 67 89',
                'adresse' => '45 Avenue du Bâtiment',
                'ville' => 'Lyon',
                'code_postal' => '69001',
            ],
            [
                'nom' => 'Moreau',
                'prenom' => 'Pierre',
                'entreprise' => null,
                'email' => 'p.moreau@email.fr',
                'telephone' => '06 34 56 78 90',
                'adresse' => '8 Boulevard Victor Hugo',
                'ville' => 'Marseille',
                'code_postal' => '13001',
            ],
            [
                'nom' => 'Simon',
                'prenom' => 'Sophie',
                'entreprise' => 'Simon Rénovation',
                'email' => 'sophie@simon-renovation.fr',
                'telephone' => '06 45 67 89 01',
                'adresse' => '23 Rue des Artisans',
                'ville' => 'Toulouse',
                'code_postal' => '31000',
            ],
            [
                'nom' => 'Laurent',
                'prenom' => 'Thomas',
                'entreprise' => null,
                'email' => 'thomas.laurent@email.fr',
                'telephone' => '06 56 78 90 12',
                'adresse' => '67 Place de la République',
                'ville' => 'Nantes',
                'code_postal' => '44000',
            ],
            [
                'nom' => 'Roux',
                'prenom' => 'Isabelle',
                'entreprise' => 'Roux & Associés',
                'email' => 'contact@roux-associes.fr',
                'telephone' => '06 67 89 01 23',
                'adresse' => '34 Avenue de la Liberté',
                'ville' => 'Nice',
                'code_postal' => '06000',
            ],
            [
                'nom' => 'Fournier',
                'prenom' => 'Luc',
                'entreprise' => null,
                'email' => 'luc.fournier@email.fr',
                'telephone' => '06 78 90 12 34',
                'adresse' => '56 Rue du Commerce',
                'ville' => 'Strasbourg',
                'code_postal' => '67000',
            ],
            [
                'nom' => 'Girard',
                'prenom' => 'Céline',
                'entreprise' => 'Girard Maçonnerie',
                'email' => 'celine@girard-maconnerie.fr',
                'telephone' => '06 89 01 23 45',
                'adresse' => '78 Boulevard Gambetta',
                'ville' => 'Bordeaux',
                'code_postal' => '33000',
            ],
        ];

        foreach ($clients as $client) {
            Client::create(array_merge($client, [
                'pays' => 'France',
                'actif' => true,
            ]));
        }

        $this->command->info('✅ ' . count($clients) . ' clients créés avec succès !');
    }
}
