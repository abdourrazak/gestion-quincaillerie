<?php

namespace Database\Factories;

use App\Models\Categorie;
use App\Models\Fournisseur;
use App\Models\Produit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Produit>
 */
class ProduitFactory extends Factory
{
    protected $model = Produit::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $prixAchat = fake()->randomFloat(2, 5, 500);
        $marge = fake()->randomFloat(2, 1.2, 2.5); // Marge entre 20% et 150%
        $prixVente = round($prixAchat * $marge, 2);

        return [
            'nom' => fake()->words(3, true),
            'reference' => 'REF-' . fake()->unique()->numerify('######'),
            'code_barre' => fake()->optional(0.7)->ean13(),
            'categorie_id' => Categorie::factory(),
            'fournisseur_id' => fake()->optional(0.8)->randomElement([
                Fournisseur::factory(),
                null,
            ]),
            'description' => fake()->optional()->paragraph(),
            'prix_achat' => $prixAchat,
            'prix_vente' => $prixVente,
            'tva' => 20.00,
            'stock_actuel' => fake()->numberBetween(0, 200),
            'stock_minimum' => 10,
            'stock_maximum' => fake()->optional()->numberBetween(100, 500),
            'unite' => fake()->randomElement(['piece', 'sac', 'm2', 'litre', 'kg', 'metre', 'rouleau', 'boite']),
            'image_principale' => null,
            'images_supplementaires' => null,
            'actif' => true,
            'en_promotion' => false,
            'prix_promotion' => null,
            'date_debut_promotion' => null,
            'date_fin_promotion' => null,
        ];
    }

    /**
     * Produit inactif
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'actif' => false,
        ]);
    }

    /**
     * Produit en promotion
     */
    public function enPromotion(): static
    {
        return $this->state(function (array $attributes) {
            $reduction = fake()->randomFloat(2, 0.1, 0.3); // 10% à 30% de réduction
            $prixPromotion = round($attributes['prix_vente'] * (1 - $reduction), 2);

            return [
                'en_promotion' => true,
                'prix_promotion' => $prixPromotion,
                'date_debut_promotion' => now()->subDays(fake()->numberBetween(1, 7)),
                'date_fin_promotion' => now()->addDays(fake()->numberBetween(7, 30)),
            ];
        });
    }

    /**
     * Produit en rupture de stock
     */
    public function ruptureStock(): static
    {
        return $this->state(fn (array $attributes) => [
            'stock_actuel' => 0,
        ]);
    }

    /**
     * Produit avec stock faible
     */
    public function stockFaible(): static
    {
        return $this->state(fn (array $attributes) => [
            'stock_actuel' => fake()->numberBetween(1, 9),
            'stock_minimum' => 10,
        ]);
    }

    /**
     * Produit avec code-barre
     */
    public function avecCodeBarre(): static
    {
        return $this->state(fn (array $attributes) => [
            'code_barre' => fake()->ean13(),
        ]);
    }
}
