<?php

namespace Database\Seeders;

use App\Models\PerformanceReview;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PerformanceReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       if (User::count() < 2) {
            $this->command->warn('Not enough users exist to create performance reviews.');
            return;
        }

        PerformanceReview::factory()->count(250)->create();
    }
}
