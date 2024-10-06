<?php

namespace Database\Factories;

use App\Models\ProductSet;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductSetFactory extends Factory
{
    protected $model = ProductSet::class;

    public function definition()
    {
        return [
            'name' => $this->faker->word,
            'slug' => Str::slug($this->faker->word),
            'published' => true,//$this->faker->boolean
        ];
    }
}
