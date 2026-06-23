<?php

namespace Tests\Feature;

use App\Models\Issue;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IssueAssignmentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(ValidateCsrfToken::class);
    }

    public function test_user_can_be_assigned_to_an_issue(): void
    {
        $issue = Issue::factory()->for(Project::factory())->create();
        $user = User::factory()->create();

        $response = $this->postJson(route('issues.members.attach', [$issue, $user]));

        $response
            ->assertCreated()
            ->assertJsonPath('user.id', $user->id)
            ->assertJsonPath('user.assigned', true);

        $this->assertDatabaseHas('issue_user', [
            'issue_id' => $issue->id,
            'user_id' => $user->id,
        ]);
    }

    public function test_assigning_the_same_user_twice_does_not_duplicate_the_pivot(): void
    {
        $issue = Issue::factory()->for(Project::factory())->create();
        $user = User::factory()->create();

        $this->postJson(route('issues.members.attach', [$issue, $user]))->assertCreated();
        $this->postJson(route('issues.members.attach', [$issue, $user]))->assertConflict();

        $this->assertSame(1, $issue->users()->whereKey($user->id)->count());
    }

    public function test_user_can_be_detached_from_an_issue(): void
    {
        $issue = Issue::factory()->for(Project::factory())->create();
        $user = User::factory()->create();
        $issue->users()->attach($user);

        $response = $this->deleteJson(route('issues.members.detach', [$issue, $user]));

        $response
            ->assertOk()
            ->assertJsonPath('user.id', $user->id)
            ->assertJsonPath('user.assigned', false);

        $this->assertDatabaseMissing('issue_user', [
            'issue_id' => $issue->id,
            'user_id' => $user->id,
        ]);
    }

    public function test_assignment_belongs_to_the_correct_issue(): void
    {
        $targetIssue = Issue::factory()->for(Project::factory())->create();
        $otherIssue = Issue::factory()->for(Project::factory())->create();
        $user = User::factory()->create();

        $this->postJson(route('issues.members.attach', [$targetIssue, $user]))->assertCreated();

        $this->assertDatabaseHas('issue_user', [
            'issue_id' => $targetIssue->id,
            'user_id' => $user->id,
        ]);
        $this->assertDatabaseMissing('issue_user', [
            'issue_id' => $otherIssue->id,
            'user_id' => $user->id,
        ]);
    }

    public function test_invalid_issue_or_user_returns_not_found(): void
    {
        $issue = Issue::factory()->for(Project::factory())->create();
        $user = User::factory()->create();

        $this->postJson("/issues/999999/members/{$user->id}")->assertNotFound();
        $this->postJson("/issues/{$issue->id}/members/999999")->assertNotFound();
    }
}
