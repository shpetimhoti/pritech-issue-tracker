<?php

namespace Tests\Feature;

use App\Models\Issue;
use App\Models\Project;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IssueSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_search_finds_issue_by_title(): void
    {
        $matchingIssue = Issue::factory()->for(Project::factory())->create([
            'title' => 'Investigate needle-alpha outage',
            'description' => 'Routine description.',
        ]);
        Issue::factory()->for(Project::factory())->create([
            'title' => 'Unrelated issue',
            'description' => 'Routine description.',
        ]);

        $response = $this->get(route('issues.index', ['search' => 'needle-alpha']));

        $response
            ->assertOk()
            ->assertSee($matchingIssue->title)
            ->assertDontSee('Unrelated issue');
    }

    public function test_search_finds_issue_by_description(): void
    {
        $matchingIssue = Issue::factory()->for(Project::factory())->create([
            'title' => 'Description match issue',
            'description' => 'The details include needle-beta for search.',
        ]);
        Issue::factory()->for(Project::factory())->create([
            'title' => 'Different issue',
            'description' => 'No matching text here.',
        ]);

        $response = $this->get(route('issues.index', ['search' => 'needle-beta']));

        $response
            ->assertOk()
            ->assertSee($matchingIssue->title)
            ->assertDontSee('Different issue');
    }

    public function test_search_no_match_response_shows_empty_state(): void
    {
        Issue::factory()->for(Project::factory())->create([
            'title' => 'Existing issue',
            'description' => 'Existing description.',
        ]);

        $response = $this->get(route('issues.index', ['search' => 'needle-missing']));

        $response
            ->assertOk()
            ->assertSee('No issues found')
            ->assertSee('0 issues');
    }

    public function test_search_combines_with_status_filter(): void
    {
        $matchingIssue = Issue::factory()->for(Project::factory())->create([
            'title' => 'needle-status open issue',
            'status' => 'open',
        ]);
        Issue::factory()->for(Project::factory())->create([
            'title' => 'needle-status closed issue',
            'status' => 'closed',
        ]);

        $response = $this->get(route('issues.index', [
            'search' => 'needle-status',
            'status' => 'open',
        ]));

        $response
            ->assertOk()
            ->assertSee($matchingIssue->title)
            ->assertDontSee('needle-status closed issue');
    }

    public function test_search_combines_with_priority_filter(): void
    {
        $matchingIssue = Issue::factory()->for(Project::factory())->create([
            'title' => 'needle-priority high issue',
            'priority' => 'high',
        ]);
        Issue::factory()->for(Project::factory())->create([
            'title' => 'needle-priority low issue',
            'priority' => 'low',
        ]);

        $response = $this->get(route('issues.index', [
            'search' => 'needle-priority',
            'priority' => 'high',
        ]));

        $response
            ->assertOk()
            ->assertSee($matchingIssue->title)
            ->assertDontSee('needle-priority low issue');
    }

    public function test_search_combines_with_tag_filter(): void
    {
        $tag = Tag::factory()->create(['name' => 'Backend']);
        $matchingIssue = Issue::factory()->for(Project::factory())->create([
            'title' => 'needle-tag backend issue',
        ]);
        $matchingIssue->tags()->attach($tag);

        Issue::factory()->for(Project::factory())->create([
            'title' => 'needle-tag frontend issue',
        ]);

        $response = $this->get(route('issues.index', [
            'search' => 'needle-tag',
            'tag' => $tag->id,
        ]));

        $response
            ->assertOk()
            ->assertSee($matchingIssue->title)
            ->assertDontSee('needle-tag frontend issue');
    }

    public function test_ajax_request_returns_only_issue_results_partial(): void
    {
        $issue = Issue::factory()->for(Project::factory())->create([
            'title' => 'needle-partial issue',
        ]);

        $response = $this->withHeaders([
            'X-Requested-With' => 'XMLHttpRequest',
        ])->get(route('issues.index', ['search' => 'needle-partial']));

        $response
            ->assertOk()
            ->assertSee($issue->title)
            ->assertDontSee('<!DOCTYPE html>', false)
            ->assertDontSee('data-issue-search-form', false);
    }

    public function test_search_or_conditions_do_not_bypass_existing_filters(): void
    {
        $matchingIssue = Issue::factory()->for(Project::factory())->create([
            'title' => 'Allowed issue',
            'description' => 'Contains needle-grouped and has the right status.',
            'status' => 'open',
        ]);
        Issue::factory()->for(Project::factory())->create([
            'title' => 'needle-grouped closed issue',
            'description' => 'This title matches but status should exclude it.',
            'status' => 'closed',
        ]);
        Issue::factory()->for(Project::factory())->create([
            'title' => 'Open unrelated issue',
            'description' => 'This has the right status but no search term.',
            'status' => 'open',
        ]);

        $response = $this->get(route('issues.index', [
            'search' => 'needle-grouped',
            'status' => 'open',
        ]));

        $response
            ->assertOk()
            ->assertSee($matchingIssue->title)
            ->assertDontSee('needle-grouped closed issue')
            ->assertDontSee('Open unrelated issue');
    }
}
