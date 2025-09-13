<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            DepartmentSeeder::class,
            UserSeeder::class,
            ProjectSeeder::class,
            TrainingSeeder::class,
            TaskSeeder::class,
            PerformanceReviewSeeder::class,
            TrainingAssignmentSeeder::class,
        ]);
    }
}
