<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
    protected $model = Task::class;

    public function definition(): array
    {
        $project = Project::inRandomOrder()->first() ?? Project::factory()->create();

        $user = User::where('department_id', $project->department_id)->inRandomOrder()->first()
            ?? User::factory()->create(['department_id' => $project->department_id]);

        $projectStartDate = \Carbon\Carbon::parse($project->start_date);
        $projectEndDate = $project->end_date ? \Carbon\Carbon::parse($project->end_date) : now()->addMonths(2);
        $dueDate = $this->faker->dateTimeBetween($projectStartDate->addWeek(), $projectEndDate);

        $status = $this->faker->randomElement(['pending', 'in_progress', 'completed']);
        $completedAt = null;

        if ($status === 'completed') {
            $completedAt = $this->faker->dateTimeBetween($projectStartDate, now());
        }

        return [
            'title' => $this->faker->sentence(4),
            'description' => $this->faker->paragraph(1),
            'project_id' => $project->id,
            'assigned_to' => $user->id,
            'status' => $status,
            'due_date' => $dueDate,
            'completed_at' => $completedAt,
        ];
    }

    public function completedOnTime(): self
    {
        return $this->state(function (array $attributes) {
            $dueDate = \Carbon\Carbon::parse($attributes['due_date']);
            return [
                'status' => 'completed',
                'completed_at' => $this->faker->dateTimeBetween($dueDate->copy()->subDays(10), $dueDate),
            ];
        });
    }

    public function completedLate(): self
    {
        return $this->state(function (array $attributes) {
            $dueDate = \Carbon\Carbon::parse($attributes['due_date']);
            return [
                'status' => 'completed',
                'completed_at' => $this->faker->dateTimeBetween($dueDate->copy()->addDay(), $dueDate->copy()->addDays(20)),
            ];
        });
    }
}