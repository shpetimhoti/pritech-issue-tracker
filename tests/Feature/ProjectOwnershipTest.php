<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectOwnershipTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(ValidateCsrfToken::class);
    }

    public function test_guest_cannot_access_project_creation(): void
    {
        $this->get(route('projects.create'))->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_create_a_project_and_becomes_its_owner(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();

        $response = $this->actingAs($owner)->post(route('projects.store'), [
            'name' => 'Owned Project',
            'description' => 'A project owned by the authenticated user.',
            'user_id' => $otherUser->id,
        ]);

        $project = Project::where('name', 'Owned Project')->first();

        $response->assertRedirect(route('projects.show', $project));
        $this->assertSame($owner->id, $project->user_id);
    }

    public function test_owner_can_edit_update_and_delete_their_project(): void
    {
        $owner = User::factory()->create();
        $project = Project::factory()->for($owner, 'owner')->create();

        $this->actingAs($owner)->get(route('projects.edit', $project))->assertOk();

        $this->actingAs($owner)->put(route('projects.update', $project), [
            'name' => 'Updated Project',
            'description' => 'This project was updated by its owner.',
        ])->assertRedirect(route('projects.show', $project));

        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'name' => 'Updated Project',
            'user_id' => $owner->id,
        ]);

        $this->actingAs($owner)->delete(route('projects.destroy', $project))->assertRedirect(route('projects.index'));
        $this->assertDatabaseMissing('projects', ['id' => $project->id]);
    }

    public function test_another_authenticated_user_receives_403_for_owner_actions(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $project = Project::factory()->for($owner, 'owner')->create();

        $this->actingAs($otherUser)->get(route('projects.edit', $project))->assertForbidden();
        $this->actingAs($otherUser)->put(route('projects.update', $project), [
            'name' => 'Illegal Update',
            'description' => 'This update should not be authorized.',
        ])->assertForbidden();
        $this->actingAs($otherUser)->delete(route('projects.destroy', $project))->assertForbidden();

        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'user_id' => $owner->id,
        ]);
    }

    public function test_edit_and_delete_buttons_are_not_visible_to_a_non_owner(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $project = Project::factory()->for($owner, 'owner')->create();

        $this->actingAs($otherUser)
            ->get(route('projects.show', $project))
            ->assertOk()
            ->assertDontSee(route('projects.edit', $project), false)
            ->assertDontSee('Delete', false);
    }

    public function test_project_index_and_show_remain_publicly_accessible(): void
    {
        $project = Project::factory()->create();

        $this->get(route('projects.index'))->assertOk();
        $this->get(route('projects.show', $project))->assertOk();
    }
}
