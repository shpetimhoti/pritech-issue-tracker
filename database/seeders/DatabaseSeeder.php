<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Issue;
use App\Models\Project;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $users = User::factory(8)->create();

        $users->push(User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]));

        $tags = Tag::factory(8)->create();
        $statuses = collect(Issue::STATUSES);
        $priorities = collect(Issue::PRIORITIES);

        Project::factory(5)
            ->sequence(fn ($sequence) => ['user_id' => $users[$sequence->index % $users->count()]->id])
            ->create()
            ->each(function (Project $project) use ($tags, $users, $statuses, $priorities): void {
            Issue::factory(fake()->numberBetween(4, 8))
                ->for($project)
                ->sequence(
                    fn ($sequence) => [
                        'status' => $statuses[$sequence->index % $statuses->count()],
                        'priority' => $priorities[$sequence->index % $priorities->count()],
                    ],
                )
                ->create()
                ->each(function (Issue $issue) use ($tags, $users): void {
                    $issue->comments()->saveMany(Comment::factory(fake()->numberBetween(0, 5))->make());

                    $issue->tags()->syncWithoutDetaching(
                        $tags->random(fake()->numberBetween(1, 4))->pluck('id')->all()
                    );

                    if (fake()->boolean(70)) {
                        $issue->users()->syncWithoutDetaching(
                            $users->random(fake()->numberBetween(1, 3))->pluck('id')->all()
                        );
                    }
                });
            });
    }
}
