<?php

namespace Database\Factories;

use App\Models\Fournisseur;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Fournisseur>
 */
class FournisseurFactory extends Factory
{
    protected $model = Fournisseur::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nom' => fake()->lastName(),
            'entreprise' => fake()->company(),
            'email' => fake()->unique()->safeEmail(),
            'telephone' => fake()->phoneNumber(),
            'telephone_secondaire' => fake()->optional()->phoneNumber(),
            'adresse' => fake()->streetAddress(),
            'ville' => fake()->city(),
            'code_postal' => fake()->postcode(),
            'pays' => 'France',
            'conditions_paiement' => fake()->randomElement([
                '30 jours fin de mois',
                '60 jours',
                'Comptant',
                '45 jours',
            ]),
            'notes' => fake()->optional()->sentence(),
            'actif' => true,
        ];
    }

    /**
     * Fournisseur inactif
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'actif' => false,
        ]);
    }
}
