<?php

namespace Tests\Feature;

use App\Models\Issue;
use App\Models\Project;
use App\Models\Tag;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TagManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(ValidateCsrfToken::class);
    }

    public function test_tag_can_be_created(): void
    {
        $response = $this->post(route('tags.store'), [
            'name' => 'Backend',
            'color' => '#2563eb',
        ]);

        $response->assertRedirect(route('tags.index'));
        $this->assertDatabaseHas('tags', [
            'name' => 'Backend',
            'color' => '#2563eb',
        ]);
    }

    public function test_duplicate_tag_name_is_rejected(): void
    {
        Tag::factory()->create(['name' => 'Bug']);

        $response = $this->from(route('tags.index'))->post(route('tags.store'), [
            'name' => 'Bug',
            'color' => '#dc2626',
        ]);

        $response
            ->assertRedirect(route('tags.index'))
            ->assertSessionHasErrors('name');
    }

    public function test_tag_creation_returns_json_validation_errors(): void
    {
        $response = $this->postJson(route('tags.store'), [
            'name' => 'A',
            'color' => 'blue',
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name', 'color']);
    }

    public function test_tag_can_be_attached_to_an_issue(): void
    {
        $issue = Issue::factory()->for(Project::factory())->create();
        $tag = Tag::factory()->create();

        $response = $this->postJson(route('issues.tags.attach', [$issue, $tag]));

        $response
            ->assertOk()
            ->assertJsonPath('tag.id', $tag->id)
            ->assertJsonPath('tag.attached', true)
            ->assertJsonPath('tag.attach_url', route('issues.tags.attach', [$issue, $tag]))
            ->assertJsonPath('tag.detach_url', route('issues.tags.detach', [$issue, $tag]));

        $this->assertDatabaseHas('issue_tag', [
            'issue_id' => $issue->id,
            'tag_id' => $tag->id,
        ]);
    }

    public function test_attaching_the_same_tag_twice_does_not_duplicate_the_pivot(): void
    {
        $issue = Issue::factory()->for(Project::factory())->create();
        $tag = Tag::factory()->create();

        $this->postJson(route('issues.tags.attach', [$issue, $tag]))->assertOk();
        $this->postJson(route('issues.tags.attach', [$issue, $tag]))->assertConflict();

        $this->assertSame(1, $issue->tags()->whereKey($tag->id)->count());
    }

    public function test_tag_can_be_detached_from_an_issue(): void
    {
        $issue = Issue::factory()->for(Project::factory())->create();
        $tag = Tag::factory()->create();
        $issue->tags()->attach($tag);

        $response = $this->deleteJson(route('issues.tags.detach', [$issue, $tag]));

        $response
            ->assertOk()
            ->assertJsonPath('tag.id', $tag->id)
            ->assertJsonPath('tag.attached', false)
            ->assertJsonPath('tag.attach_url', route('issues.tags.attach', [$issue, $tag]))
            ->assertJsonPath('tag.detach_url', route('issues.tags.detach', [$issue, $tag]));

        $this->assertDatabaseMissing('issue_tag', [
            'issue_id' => $issue->id,
            'tag_id' => $tag->id,
        ]);
    }
}
