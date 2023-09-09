<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Faker\Generator as Faker;
use App\Models\Category;

class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition(): array
    {
        $faker = $this->faker;

        return [
            'name' => $faker->name,
            'status' => rand(0,1),
            'slug' => $faker->unique()->slug,
        ];
    }
}
