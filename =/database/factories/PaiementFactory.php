<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Paiement;
use App\Models\Dette;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Paiement>
 */
class PaiementFactory extends Factory
{
    protected $model = Paiement::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'dette_id' => Dette::factory(),
            'montant' => $this->faker->randomFloat(2, 100, 10000),
            'date' => $this->faker->dateTimeThisYear(),
        ];
    }
}
