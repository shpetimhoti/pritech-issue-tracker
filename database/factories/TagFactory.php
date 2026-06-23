<?php

namespace Database\Factories;

use App\Models\Tag;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Tag>
 */
class TagFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->randomElement([
                'bug',
                'feature',
                'backend',
                'frontend',
                'design',
                'documentation',
                'performance',
                'security',
                'qa',
                'support',
            ]).'-'.fake()->unique()->numberBetween(1000, 999999),
            'color' => fake()->hexColor(),
        ];
    }
}
