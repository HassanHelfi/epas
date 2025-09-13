<?php

namespace Database\Factories;

use App\Models\Department;
use App\Models\PerformanceReview;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PerformanceReviewFactory extends Factory
{
    protected $model = PerformanceReview::class;

    public function definition(): array
    {
        $department = Department::has('users', '>', 1)->inRandomOrder()->first();

        if (!$department) {
            $department = Department::factory()->has(User::factory()->count(2))->create();
        }

        $users = User::where('department_id', $department->id)->inRandomOrder()->take(2)->get();
        $reviewer = $users[0];
        $reviewee = $users[1];

        return [
            'reviewer_id' => $reviewer->id,
            'reviewee_id' => $reviewee->id,
            'score' => $this->faker->numberBetween(5, 10),
            'comments' => $this->faker->paragraph(2),
            'review_date' => $this->faker->dateTimeBetween('-12 months', 'now'),
        ];
    }
}