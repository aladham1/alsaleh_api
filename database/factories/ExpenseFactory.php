<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Expense;
use App\Models\Project;

class ExpenseFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Expense::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'total' => $this->faker->randomFloat(0, 0, 500),
            'paid_at' => $this->faker->dateTime(),
        ];
    }
}
