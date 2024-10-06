<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition()
    {
        return [
            'name' => $this->faker->word,
            'sku' => $this->faker->unique()->word,
            'type' => $this->faker->randomElement(['device', 'service']),
            'condition' => $this->faker->randomElement(['new', 'used', 'refurbished']),
            'description_title' => $this->faker->sentence,
            'description_text' => $this->faker->paragraph,
            'price' => $this->faker->randomFloat(2, 10, 100),
            'wholesale_price' => $this->faker->randomFloat(2, 5, 50),
            'published' => true,//$this->faker->boolean
        ];
    }
}
