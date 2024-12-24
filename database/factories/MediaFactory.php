<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Media;

class MediaFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Media::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'morphs_id' => $this->faker->randomDigitNotNull,
            'morphs_type' => $this->faker->word,
            'type' => $this->faker->randomElement(["image","video"]),
        ];
    }
}
