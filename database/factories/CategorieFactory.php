<?php

namespace Database\Factories;

use App\Models\Categorie;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Categorie>
 */
class CategorieFactory extends Factory
{
    protected $model = Categorie::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $nom = fake()->words(2, true);

        return [
            'nom' => $nom,
            'slug' => Str::slug($nom) . '-' . fake()->unique()->numberBetween(1000, 9999),
            'description' => fake()->sentence(10),
            'icone' => fake()->randomElement(['Hammer', 'Wrench', 'Paintbrush', 'Zap', 'Home']),
            'couleur' => fake()->hexColor(),
            'actif' => true,
        ];
    }

    /**
     * Catégorie inactive
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'actif' => false,
        ]);
    }
}
