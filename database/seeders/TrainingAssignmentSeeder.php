<?php

namespace Database\Seeders;

use App\Models\Training;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TrainingAssignmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $trainings = Training::all();
        $currentYearStart = now()->startOfYear();

        if ($trainings->isEmpty() || $users->isEmpty()) {
            $this->command->warn('Users or Trainings not found. Please run UserSeeder and TrainingSeeder first.');
            return;
        }

        foreach ($users as $user) {
            $assignedTrainings = $trainings->random(rand(2, 4));

            foreach ($assignedTrainings as $training) {
                $assignedDate = Carbon::createFromTimestamp(rand($currentYearStart->timestamp, now()->timestamp));

                $isRequired = $this->faker_generator()->boolean(80);
                $status = 'assigned';
                $completedAt = null;

                $chance = rand(1, 100);
                if ($chance <= 75) {
                    $status = 'completed';
                    $completionDate = (clone $assignedDate)->addDays(rand(5, 45));
                    $completedAt = $completionDate->isFuture() ? now() : $completionDate;
                } elseif ($chance <= 90) {
                    $status = 'in_progress';
                }

                $user->trainings()->attach($training->id, [
                    'is_required' => $isRequired,
                    'status' => $status,
                    'assigned_date' => $assignedDate,
                    'completed_at' => $completedAt,
                ]);
            }
        }
    }
    
    private function faker_generator()
    {
        return \Faker\Factory::create();
    }
}
