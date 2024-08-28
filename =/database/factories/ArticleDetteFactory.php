<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ArticleDette>
 */
class ArticleDetteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'article_id' => \App\Models\Article::factory(),
            'dette_id' => \App\Models\Dette::factory(),
            'qteVente' => $this->faker->numberBetween(1, 10),
            'prixVente' => $this->faker->randomFloat(2, 100, 1000),
    
        ];
    }
}
