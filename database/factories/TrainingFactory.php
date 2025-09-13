<?php

namespace Database\Factories;

use App\Models\Training;
use Illuminate\Database\Eloquent\Factories\Factory;

class TrainingFactory extends Factory
{
    protected $model = Training::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->unique()->randomElement([
                'Information Security & Data Privacy', 'Workplace Safety Essentials', 'Anti-Harassment & Ethics',
                'Compliance & Regulatory Basics', 'Cybersecurity Best Practices', 'Project Management Fundamentals',
                'Leadership & Team Development', 'Financial Planning & Budgeting', 'Customer Service Excellence',
                'Digital Marketing Strategies', 'Data Analysis & Reporting', 'Quality Assurance & Testing',
                'Agile Development Methodology', 'Risk Management & Assessment', 'Communication Skills Training',
                'Advanced Laravel Techniques', 'Vue.js for Modern UIs', 'Cloud Infrastructure with AWS'
            ]),
            'description' => $this->faker->sentence(15),
        ];
    }
}