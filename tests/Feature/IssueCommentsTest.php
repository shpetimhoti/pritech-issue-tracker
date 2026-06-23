<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Issue;
use App\Models\Project;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IssueCommentsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(ValidateCsrfToken::class);
    }

    public function test_comments_load_as_json_newest_first(): void
    {
        $issue = Issue::factory()->for(Project::factory())->create();
        Comment::factory()->for($issue)->create([
            'author_name' => 'Older Author',
            'body' => 'Older comment',
            'created_at' => now()->subDay(),
        ]);
        Comment::factory()->for($issue)->create([
            'author_name' => 'Newer Author',
            'body' => 'Newer comment',
            'created_at' => now(),
        ]);

        $response = $this->getJson(route('issues.comments.index', $issue));

        $response
            ->assertOk()
            ->assertJsonPath('data.0.author_name', 'Newer Author')
            ->assertJsonPath('data.1.author_name', 'Older Author')
            ->assertJsonPath('meta.total', 2);
    }

    public function test_comments_are_paginated(): void
    {
        $issue = Issue::factory()->for(Project::factory())->create();
        Comment::factory(6)->for($issue)->create();

        $response = $this->getJson(route('issues.comments.index', $issue));

        $response
            ->assertOk()
            ->assertJsonCount(5, 'data')
            ->assertJsonPath('meta.per_page', 5)
            ->assertJsonPath('meta.total', 6);

        $this->assertNotNull($response->json('links.next'));
    }

    public function test_valid_comment_can_be_created(): void
    {
        $issue = Issue::factory()->for(Project::factory())->create();

        $response = $this->postJson(route('issues.comments.store', $issue), [
            'author_name' => 'Taylor',
            'body' => 'This needs one more reproduction step.',
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('comment.author_name', 'Taylor')
            ->assertJsonPath('comment.body', 'This needs one more reproduction step.');

        $this->assertDatabaseHas('comments', [
            'issue_id' => $issue->id,
            'author_name' => 'Taylor',
            'body' => 'This needs one more reproduction step.',
        ]);
    }

    public function test_missing_author_name_returns_validation_error(): void
    {
        $issue = Issue::factory()->for(Project::factory())->create();

        $response = $this->postJson(route('issues.comments.store', $issue), [
            'body' => 'Looks good.',
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors('author_name');
    }

    public function test_missing_body_returns_validation_error(): void
    {
        $issue = Issue::factory()->for(Project::factory())->create();

        $response = $this->postJson(route('issues.comments.store', $issue), [
            'author_name' => 'Taylor',
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors('body');
    }

    public function test_comment_belongs_to_the_correct_issue(): void
    {
        $targetIssue = Issue::factory()->for(Project::factory())->create();
        $otherIssue = Issue::factory()->for(Project::factory())->create();

        $this->postJson(route('issues.comments.store', $targetIssue), [
            'author_name' => 'Taylor',
            'body' => 'Only the target issue should receive this comment.',
        ])->assertCreated();

        $this->assertSame(1, $targetIssue->comments()->count());
        $this->assertSame(0, $otherIssue->comments()->count());
    }
}
