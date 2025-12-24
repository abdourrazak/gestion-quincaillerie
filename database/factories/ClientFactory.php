<?php

namespace Database\Factories;

use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Client>
 */
class ClientFactory extends Factory
{
    protected $model = Client::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nom' => fake()->lastName(),
            'prenom' => fake()->firstName(),
            'entreprise' => fake()->optional(0.3)->company(),
            'email' => fake()->optional(0.7)->safeEmail(),
            'telephone' => fake()->phoneNumber(),
            'telephone_secondaire' => fake()->optional(0.3)->phoneNumber(),
            'adresse' => fake()->optional()->streetAddress(),
            'ville' => fake()->optional()->city(),
            'code_postal' => fake()->optional()->postcode(),
            'pays' => 'France',
            'notes' => fake()->optional()->sentence(),
            'actif' => true,
        ];
    }

    /**
     * Client professionnel (avec entreprise)
     */
    public function professionnel(): static
    {
        return $this->state(fn (array $attributes) => [
            'entreprise' => fake()->company(),
        ]);
    }

    /**
     * Client particulier (sans entreprise)
     */
    public function particulier(): static
    {
        return $this->state(fn (array $attributes) => [
            'entreprise' => null,
        ]);
    }

    /**
     * Client inactif
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'actif' => false,
        ]);
    }
}
