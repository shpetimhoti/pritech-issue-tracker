<?php

namespace Database\Factories;

use App\Models\Issue;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Issue>
 */
class IssueFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'title' => fake()->randomElement([
                'Fix login redirect after password reset',
                'Improve empty state messaging',
                'Add validation for exported reports',
                'Resolve duplicate notification delivery',
                'Optimize project list query',
                'Update issue filtering behavior',
                'Handle missing attachment metadata',
                'Polish mobile form layout',
                'Document API error responses',
                'Investigate intermittent queue timeout',
            ]),
            'description' => fake()->paragraphs(2, true),
            'status' => fake()->randomElement(Issue::STATUSES),
            'priority' => fake()->randomElement(Issue::PRIORITIES),
            'due_date' => fake()->optional(0.75)->dateTimeBetween('now', '+3 months'),
        ];
    }
}
