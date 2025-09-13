<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = Department::all();

        if ($departments->isEmpty()) {
            $this->command->warn('No departments found. Please run the DepartmentSeeder first.');
            return;
        }

        foreach ($departments as $department) {
            User::factory()->count(10)->create([
                'department_id' => $department->id,
            ]);
        }
    }
}
