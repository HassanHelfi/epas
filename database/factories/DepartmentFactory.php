<?php

namespace Database\Factories;

use App\Models\Department;
use Illuminate\Database\Eloquent\Factories\Factory;

class DepartmentFactory extends Factory
{
    protected $model = Department::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->randomElement([
                'Engineering', 'Marketing', 'Sales', 'Human Resources', 'Finance',
                'Operations', 'Product Management', 'Quality Assurance', 'IT Support', 'Customer Success'
            ]),
            'description' => $this->faker->sentence(10),
            'manager_id' => null,
        ];
    }
}