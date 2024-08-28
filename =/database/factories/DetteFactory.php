<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Dette>
 */
class DetteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'date' => $this->faker->date(),
            'montant' => $this->faker->randomFloat(2, 100, 1000),
            'montantDu' => $this->faker->randomFloat(2, 100, 1000),
            'montantRestant' => $this->faker->randomFloat(2, 0, 900),
            'client_id' => \App\Models\Client::factory(),
        ];
    }
}
