<?php

namespace Database\Seeders;

use App\Models\Categorie;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorieSeeder extends Seeder
{
    /**
     * Créer les catégories de matériaux de construction
     */
    public function run(): void
    {
        $categories = [
            [
                'nom' => 'Ciment & Béton',
                'description' => 'Ciments, bétons prêts à l\'emploi, mortiers et enduits pour tous vos travaux de maçonnerie',
                'icone' => 'Package',
                'couleur' => '#6B7280',
            ],
            [
                'nom' => 'Bois & Planches',
                'description' => 'Planches, poutres, chevrons et bois de charpente pour la construction et la menuiserie',
                'icone' => 'Trees',
                'couleur' => '#92400E',
            ],
            [
                'nom' => 'Quincaillerie',
                'description' => 'Vis, clous, boulons, écrous, chevilles et tous les accessoires de fixation',
                'icone' => 'Wrench',
                'couleur' => '#374151',
            ],
            [
                'nom' => 'Portes & Fenêtres',
                'description' => 'Portes intérieures et extérieures, fenêtres, volets et accessoires',
                'icone' => 'DoorOpen',
                'couleur' => '#1F2937',
            ],
            [
                'nom' => 'Peinture & Revêtements',
                'description' => 'Peintures murales, vernis, lasures, enduits décoratifs et accessoires de peinture',
                'icone' => 'Paintbrush',
                'couleur' => '#DC2626',
            ],
            [
                'nom' => 'Électricité',
                'description' => 'Câbles, prises, interrupteurs, disjoncteurs et matériel électrique',
                'icone' => 'Zap',
                'couleur' => '#F59E0B',
            ],
            [
                'nom' => 'Plomberie',
                'description' => 'Tuyaux, raccords, robinetterie, sanitaires et accessoires de plomberie',
                'icone' => 'Droplet',
                'couleur' => '#3B82F6',
            ],
            [
                'nom' => 'Briques & Blocs',
                'description' => 'Briques, parpaings, blocs de béton et pierres pour la construction',
                'icone' => 'Box',
                'couleur' => '#EF4444',
            ],
            [
                'nom' => 'Toiture',
                'description' => 'Tuiles, ardoises, tôles, gouttières et accessoires de toiture',
                'icone' => 'Home',
                'couleur' => '#7C3AED',
            ],
            [
                'nom' => 'Outils',
                'description' => 'Outils à main, outils électriques et équipements pour professionnels et particuliers',
                'icone' => 'Hammer',
                'couleur' => '#059669',
            ],
        ];

        foreach ($categories as $categorie) {
            Categorie::create([
                'nom' => $categorie['nom'],
                'slug' => Str::slug($categorie['nom']),
                'description' => $categorie['description'],
                'icone' => $categorie['icone'],
                'couleur' => $categorie['couleur'],
                'actif' => true,
            ]);
        }

        $this->command->info('✅ ' . count($categories) . ' catégories créées avec succès !');
    }
}
