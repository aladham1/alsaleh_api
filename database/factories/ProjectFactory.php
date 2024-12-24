<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Project;

class ProjectFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Project::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
            'description' => $this->faker->text,
            'avatar' => $this->faker->word,
            'total_paid' => $this->faker->randomFloat(0, 0, 500),
            'total_requested' => $this->faker->randomFloat(0, 0, 500),
            'total_remaining' => $this->faker->randomFloat(0, 0, 500),
            'min_donation_fee' => $this->faker->randomFloat(0, 0, 500),
            'increment_by' => $this->faker->randomFloat(0, 0, 500),
            'bank_name' => $this->faker->word,
            'bank_branch' => $this->faker->word,
            'bank_iban' => $this->faker->word,
            'country' => $this->faker->country,
            'city' => $this->faker->city,
            'gov' => $this->faker->word,
            'lat' => $this->faker->latitude,
            'lng' => $this->faker->longitude,
            'status' => $this->faker->randomElement(["active","archived"]),
        ];
    }
}
