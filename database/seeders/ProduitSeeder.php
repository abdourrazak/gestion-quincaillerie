<?php

namespace Database\Seeders;

use App\Models\Categorie;
use App\Models\Fournisseur;
use App\Models\Produit;
use Illuminate\Database\Seeder;

class ProduitSeeder extends Seeder
{
    /**
     * Créer des produits réalistes pour chaque catégorie
     */
    public function run(): void
    {
        $categories = Categorie::all();
        $fournisseurs = Fournisseur::all();

        $produits = [
            // Ciment & Béton
            [
                'categorie' => 'Ciment & Béton',
                'produits' => [
                    ['nom' => 'Ciment gris 25kg', 'prix_achat' => 5.50, 'prix_vente' => 8.90, 'stock' => 200, 'unite' => 'sac'],
                    ['nom' => 'Ciment blanc 25kg', 'prix_achat' => 8.00, 'prix_vente' => 12.50, 'stock' => 80, 'unite' => 'sac'],
                    ['nom' => 'Béton prêt à l\'emploi 30kg', 'prix_achat' => 4.20, 'prix_vente' => 6.90, 'stock' => 150, 'unite' => 'sac'],
                    ['nom' => 'Mortier colle 25kg', 'prix_achat' => 6.50, 'prix_vente' => 10.50, 'stock' => 120, 'unite' => 'sac'],
                    ['nom' => 'Enduit de façade 25kg', 'prix_achat' => 12.00, 'prix_vente' => 18.90, 'stock' => 60, 'unite' => 'sac'],
                ],
            ],
            // Bois & Planches
            [
                'categorie' => 'Bois & Planches',
                'produits' => [
                    ['nom' => 'Planche sapin 200x20x2cm', 'prix_achat' => 8.50, 'prix_vente' => 14.90, 'stock' => 50, 'unite' => 'piece'],
                    ['nom' => 'Poutre chêne 300x15x15cm', 'prix_achat' => 45.00, 'prix_vente' => 75.00, 'stock' => 20, 'unite' => 'piece'],
                    ['nom' => 'Chevron pin 400x7x7cm', 'prix_achat' => 6.00, 'prix_vente' => 10.50, 'stock' => 100, 'unite' => 'piece'],
                    ['nom' => 'Contreplaqué 250x125cm', 'prix_achat' => 25.00, 'prix_vente' => 42.00, 'stock' => 30, 'unite' => 'piece'],
                    ['nom' => 'Lambris pin 2.5m', 'prix_achat' => 3.50, 'prix_vente' => 6.20, 'stock' => 200, 'unite' => 'piece'],
                ],
            ],
            // Quincaillerie
            [
                'categorie' => 'Quincaillerie',
                'produits' => [
                    ['nom' => 'Vis acier 4x40mm (boîte 200)', 'prix_achat' => 3.50, 'prix_vente' => 5.90, 'stock' => 150, 'unite' => 'boite'],
                    ['nom' => 'Clous 50mm (kg)', 'prix_achat' => 4.00, 'prix_vente' => 6.50, 'stock' => 80, 'unite' => 'kg'],
                    ['nom' => 'Chevilles nylon 8mm (boîte 100)', 'prix_achat' => 2.50, 'prix_vente' => 4.20, 'stock' => 200, 'unite' => 'boite'],
                    ['nom' => 'Boulons M10 (boîte 50)', 'prix_achat' => 8.00, 'prix_vente' => 13.50, 'stock' => 60, 'unite' => 'boite'],
                    ['nom' => 'Écrous M8 (boîte 100)', 'prix_achat' => 3.00, 'prix_vente' => 5.00, 'stock' => 100, 'unite' => 'boite'],
                ],
            ],
            // Portes & Fenêtres
            [
                'categorie' => 'Portes & Fenêtres',
                'produits' => [
                    ['nom' => 'Porte intérieure 83x204cm', 'prix_achat' => 65.00, 'prix_vente' => 110.00, 'stock' => 15, 'unite' => 'piece'],
                    ['nom' => 'Fenêtre PVC 120x100cm', 'prix_achat' => 180.00, 'prix_vente' => 299.00, 'stock' => 8, 'unite' => 'piece'],
                    ['nom' => 'Volet roulant électrique', 'prix_achat' => 250.00, 'prix_vente' => 420.00, 'stock' => 5, 'unite' => 'piece'],
                    ['nom' => 'Poignée de porte chromée', 'prix_achat' => 12.00, 'prix_vente' => 19.90, 'stock' => 50, 'unite' => 'piece'],
                    ['nom' => 'Serrure 3 points', 'prix_achat' => 85.00, 'prix_vente' => 145.00, 'stock' => 12, 'unite' => 'piece'],
                ],
            ],
            // Peinture & Revêtements
            [
                'categorie' => 'Peinture & Revêtements',
                'produits' => [
                    ['nom' => 'Peinture blanche mat 10L', 'prix_achat' => 35.00, 'prix_vente' => 58.00, 'stock' => 40, 'unite' => 'piece'],
                    ['nom' => 'Peinture acrylique couleur 2.5L', 'prix_achat' => 18.00, 'prix_vente' => 29.90, 'stock' => 60, 'unite' => 'piece'],
                    ['nom' => 'Vernis bois incolore 1L', 'prix_achat' => 12.00, 'prix_vente' => 19.50, 'stock' => 50, 'unite' => 'litre'],
                    ['nom' => 'Rouleau peinture 25cm', 'prix_achat' => 3.50, 'prix_vente' => 6.20, 'stock' => 100, 'unite' => 'piece'],
                    ['nom' => 'Pinceau plat 5cm', 'prix_achat' => 2.50, 'prix_vente' => 4.50, 'stock' => 80, 'unite' => 'piece'],
                ],
            ],
            // Électricité
            [
                'categorie' => 'Électricité',
                'produits' => [
                    ['nom' => 'Câble électrique 2.5mm² (rouleau 100m)', 'prix_achat' => 45.00, 'prix_vente' => 75.00, 'stock' => 20, 'unite' => 'rouleau'],
                    ['nom' => 'Prise encastrable blanche', 'prix_achat' => 2.50, 'prix_vente' => 4.50, 'stock' => 150, 'unite' => 'piece'],
                    ['nom' => 'Interrupteur va-et-vient', 'prix_achat' => 3.00, 'prix_vente' => 5.20, 'stock' => 120, 'unite' => 'piece'],
                    ['nom' => 'Disjoncteur 20A', 'prix_achat' => 8.50, 'prix_vente' => 14.50, 'stock' => 40, 'unite' => 'piece'],
                    ['nom' => 'Ampoule LED E27 10W', 'prix_achat' => 3.50, 'prix_vente' => 6.90, 'stock' => 200, 'unite' => 'piece'],
                ],
            ],
            // Plomberie
            [
                'categorie' => 'Plomberie',
                'produits' => [
                    ['nom' => 'Tuyau PVC Ø100mm (barre 2m)', 'prix_achat' => 8.00, 'prix_vente' => 13.50, 'stock' => 50, 'unite' => 'piece'],
                    ['nom' => 'Robinet mitigeur cuisine', 'prix_achat' => 35.00, 'prix_vente' => 59.00, 'stock' => 15, 'unite' => 'piece'],
                    ['nom' => 'Siphon lavabo chromé', 'prix_achat' => 8.50, 'prix_vente' => 14.90, 'stock' => 30, 'unite' => 'piece'],
                    ['nom' => 'Raccord PVC Ø40mm', 'prix_achat' => 1.50, 'prix_vente' => 2.80, 'stock' => 200, 'unite' => 'piece'],
                    ['nom' => 'Flexible douche 1.5m', 'prix_achat' => 6.00, 'prix_vente' => 10.50, 'stock' => 40, 'unite' => 'piece'],
                ],
            ],
            // Briques & Blocs
            [
                'categorie' => 'Briques & Blocs',
                'produits' => [
                    ['nom' => 'Parpaing creux 20x20x50cm', 'prix_achat' => 1.20, 'prix_vente' => 2.10, 'stock' => 500, 'unite' => 'piece'],
                    ['nom' => 'Brique rouge pleine', 'prix_achat' => 0.80, 'prix_vente' => 1.40, 'stock' => 800, 'unite' => 'piece'],
                    ['nom' => 'Bloc béton cellulaire 60x25cm', 'prix_achat' => 3.50, 'prix_vente' => 6.20, 'stock' => 200, 'unite' => 'piece'],
                    ['nom' => 'Pierre de parement (m²)', 'prix_achat' => 25.00, 'prix_vente' => 42.00, 'stock' => 50, 'unite' => 'm2'],
                    ['nom' => 'Linteau béton 2m', 'prix_achat' => 18.00, 'prix_vente' => 30.00, 'stock' => 30, 'unite' => 'piece'],
                ],
            ],
            // Toiture
            [
                'categorie' => 'Toiture',
                'produits' => [
                    ['nom' => 'Tuile terre cuite', 'prix_achat' => 1.50, 'prix_vente' => 2.60, 'stock' => 1000, 'unite' => 'piece'],
                    ['nom' => 'Ardoise naturelle 30x20cm', 'prix_achat' => 2.00, 'prix_vente' => 3.50, 'stock' => 500, 'unite' => 'piece'],
                    ['nom' => 'Tôle ondulée 2m', 'prix_achat' => 15.00, 'prix_vente' => 25.00, 'stock' => 40, 'unite' => 'piece'],
                    ['nom' => 'Gouttière PVC 4m', 'prix_achat' => 12.00, 'prix_vente' => 20.00, 'stock' => 30, 'unite' => 'piece'],
                    ['nom' => 'Faîtière terre cuite', 'prix_achat' => 4.50, 'prix_vente' => 7.80, 'stock' => 100, 'unite' => 'piece'],
                ],
            ],
            // Outils
            [
                'categorie' => 'Outils',
                'produits' => [
                    ['nom' => 'Marteau rivoir 500g', 'prix_achat' => 12.00, 'prix_vente' => 19.90, 'stock' => 30, 'unite' => 'piece'],
                    ['nom' => 'Perceuse électrique 750W', 'prix_achat' => 55.00, 'prix_vente' => 95.00, 'stock' => 15, 'unite' => 'piece'],
                    ['nom' => 'Scie égoïne 50cm', 'prix_achat' => 18.00, 'prix_vente' => 30.00, 'stock' => 20, 'unite' => 'piece'],
                    ['nom' => 'Niveau à bulle 60cm', 'prix_achat' => 8.50, 'prix_vente' => 14.50, 'stock' => 25, 'unite' => 'piece'],
                    ['nom' => 'Mètre ruban 5m', 'prix_achat' => 4.50, 'prix_vente' => 7.90, 'stock' => 50, 'unite' => 'piece'],
                ],
            ],
        ];

        $count = 0;
        foreach ($produits as $groupe) {
            $categorie = $categories->firstWhere('nom', $groupe['categorie']);
            
            if (!$categorie) {
                continue;
            }

            foreach ($groupe['produits'] as $index => $produit) {
                $fournisseur = $fournisseurs->random();
                
                Produit::create([
                    'nom' => $produit['nom'],
                    'reference' => 'REF-' . strtoupper(substr($categorie->slug, 0, 3)) . '-' . str_pad($index + 1, 4, '0', STR_PAD_LEFT),
                    'code_barre' => rand(1000000000000, 9999999999999),
                    'categorie_id' => $categorie->id,
                    'fournisseur_id' => $fournisseur->id,
                    'description' => 'Produit de qualité professionnelle pour vos travaux de construction et rénovation.',
                    'prix_achat' => $produit['prix_achat'],
                    'prix_vente' => $produit['prix_vente'],
                    'tva' => 20.00,
                    'stock_actuel' => $produit['stock'],
                    'stock_minimum' => 10,
                    'stock_maximum' => $produit['stock'] * 2,
                    'unite' => $produit['unite'],
                    'actif' => true,
                    'en_promotion' => false,
                ]);
                
                $count++;
            }
        }

        $this->command->info('✅ ' . $count . ' produits créés avec succès !');
    }
}
