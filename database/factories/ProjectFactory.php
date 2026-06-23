<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Project>
 */
class ProjectFactory extends Factory
{
    public function definition(): array
    {
        $startDate = fake()->dateTimeBetween('-2 months', '+1 month');
        $deadline = fake()->dateTimeBetween($startDate, '+6 months');

        return [
            'name' => fake()->randomElement([
                    'Website Redesign',
                    'Customer Portal',
                    'Mobile Application',
                    'Internal Dashboard',
                    'Booking Platform',
                    'Inventory System',
                    'Marketing Campaign',
                    'API Integration',
                ]) . ' ' . fake()->unique()->numberBetween(100, 9999),

            'description' => fake()->paragraph(),
            'user_id' => User::factory(),
            'start_date' => $startDate,
            'deadline' => $deadline,
        ];
    }
}
