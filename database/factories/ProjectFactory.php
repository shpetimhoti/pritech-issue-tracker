<?php

namespace Database\Factories;

use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startDate = fake()->dateTimeBetween('-2 months', '+1 month');
        $deadline = fake()->dateTimeBetween($startDate, '+6 months');

        return [
            'name' => fake()->unique()->randomElement([
                'Customer Portal',
                'Billing Dashboard',
                'Support Knowledge Base',
                'Mobile Onboarding',
                'Reporting API',
                'Inventory Sync',
                'Internal Admin Tools',
                'Notification Service',
            ]),
            'description' => fake()->paragraph(3),
            'start_date' => $startDate,
            'deadline' => $deadline,
        ];
    }
}
