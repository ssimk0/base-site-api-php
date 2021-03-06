<?php

namespace Database\Factories;

use App\Models\UploadCategory;
use App\Models\UploadType;
use Illuminate\Database\Eloquent\Factories\Factory;

class UploadCategoryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = UploadCategory::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            "name" => $this->faker->name,
            "slug" => $this->faker->slug,
            "sub_path" => $this->faker->name,
            "thumbnail" => $this->faker->url,
            "type_id" => UploadType::factory()->createOne(),
            "description" => $this->faker->sentence,
        ];
    }
}
