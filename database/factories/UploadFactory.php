<?php

namespace Database\Factories;

use App\Models\Upload;
use App\Models\UploadCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class UploadFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Upload::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            "file" => $this->faker->url . '/test/test.jpg',
            "thumbnail" => $this->faker->url . '/test/test.jpg',
            "description" => $this->faker->sentence,
            "category_id" => UploadCategory::factory()->createOne()
        ];
    }
}
