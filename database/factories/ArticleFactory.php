<?php

namespace Database\Factories;

use App\Models\Article;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ArticleFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Article::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            "title" => $this->faker->title,
            "body" => $this->faker->paragraph(3),
            "short" => $this->faker->paragraph,
            "slug" => $this->faker->slug,
            "image" => $this->faker->url,
            "published" => $this->faker->boolean,
            "viewed" => $this->faker->numberBetween(0, 100),
            "user_id" => User::factory()->createOne(),
        ];
    }
}
