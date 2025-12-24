<?php

namespace Database\Seeders;

use App\Models\Fournisseur;
use Illuminate\Database\Seeder;

class FournisseurSeeder extends Seeder
{
    /**
     * Créer des fournisseurs de test
     */
    public function run(): void
    {
        $fournisseurs = [
            [
                'nom' => 'Dupont',
                'entreprise' => 'Matériaux Dupont SA',
                'email' => 'contact@dupont-materiaux.fr',
                'telephone' => '01 45 67 89 01',
                'adresse' => '15 Avenue des Bâtisseurs',
                'ville' => 'Paris',
                'code_postal' => '75015',
                'conditions_paiement' => '30 jours fin de mois',
            ],
            [
                'nom' => 'Martin',
                'entreprise' => 'Bois Martin & Fils',
                'email' => 'commercial@bois-martin.fr',
                'telephone' => '02 34 56 78 90',
                'adresse' => '42 Rue de la Scierie',
                'ville' => 'Lyon',
                'code_postal' => '69003',
                'conditions_paiement' => '45 jours',
            ],
            [
                'nom' => 'Bernard',
                'entreprise' => 'Quincaillerie Bernard',
                'email' => 'info@quincaillerie-bernard.fr',
                'telephone' => '03 45 67 89 12',
                'adresse' => '8 Place du Commerce',
                'ville' => 'Marseille',
                'code_postal' => '13001',
                'conditions_paiement' => '60 jours',
            ],
            [
                'nom' => 'Rousseau',
                'entreprise' => 'Électricité Rousseau',
                'email' => 'ventes@elec-rousseau.fr',
                'telephone' => '04 56 78 90 23',
                'adresse' => '23 Boulevard Voltaire',
                'ville' => 'Toulouse',
                'code_postal' => '31000',
                'conditions_paiement' => '30 jours fin de mois',
            ],
            [
                'nom' => 'Petit',
                'entreprise' => 'Plomberie Petit SARL',
                'email' => 'contact@plomberie-petit.fr',
                'telephone' => '05 67 89 01 34',
                'adresse' => '56 Rue des Artisans',
                'ville' => 'Nantes',
                'code_postal' => '44000',
                'conditions_paiement' => 'Comptant',
            ],
        ];

        foreach ($fournisseurs as $fournisseur) {
            Fournisseur::create(array_merge($fournisseur, [
                'pays' => 'France',
                'actif' => true,
            ]));
        }

        $this->command->info('✅ ' . count($fournisseurs) . ' fournisseurs créés avec succès !');
    }
}
