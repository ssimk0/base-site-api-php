<?php

namespace Database\Factories;

use App\Models\Page;
use App\Models\PageCategory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PageFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Page::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title' => $this->faker->sentence,
            'body' => $this->faker->paragraph(3),
            'user_id' => User::factory()->createOne(),
            'page_category_id' => PageCategory::factory()->createOne(),
            'slug' => $this->faker->slug,
            'page_id' => null,
        ];
    }
}
