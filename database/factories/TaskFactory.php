<?php

namespace Database\Factories;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    protected $model = Task::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence,
            'comment' => $this->faker->paragraph,
            'date' => $this->faker->date,
            'time_spent' => $this->faker->numberBetween(1, 240),
            'user_id' => function () {
                return auth()->id() ?? User::factory()->create()->id;
            },
        ];
    }
}
