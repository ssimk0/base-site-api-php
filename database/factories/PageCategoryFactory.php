<?php

namespace Database\Factories;

use App\Models\PageCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class PageCategoryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PageCategory::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            "name" => $this->faker->name,
            "slug" => $this->faker->slug
        ];
    }
}
