<?php

namespace Database\Factories;

use App\Models\Department;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectFactory extends Factory
{
    protected $model = Project::class;

    public function definition(): array
    {
        $startDate = $this->faker->dateTimeBetween('-2 years', '-3 months');
        $plannedEndDate = (clone $startDate)->modify('+' . rand(3, 12) . ' months');

        $isCompleted = $this->faker->boolean(70);
        $status = $isCompleted ? 'completed' : 'active';

        $completedAt = null;
        if ($isCompleted) {
            $completedAt = $this->faker->dateTimeBetween($startDate, (clone $plannedEndDate)->modify('+1 month'));
        }

        return [
            'name' => $this->faker->unique()->catchPhrase(),
            'description' => $this->faker->paragraph(2),
            'department_id' => Department::inRandomOrder()->first()->id ?? Department::factory(),
            'start_date' => $startDate,
            'end_date' => $plannedEndDate,
            'status' => $status,
            'completed_at' => $completedAt,
        ];
    }
}