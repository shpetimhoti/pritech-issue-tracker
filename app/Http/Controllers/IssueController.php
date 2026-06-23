<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreIssueRequest;
use App\Http\Requests\UpdateIssueRequest;
use App\Models\Issue;
use App\Models\Project;
use App\Models\Tag;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class IssueController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'status' => ['nullable', Rule::in(Issue::STATUSES)],
            'priority' => ['nullable', Rule::in(Issue::PRIORITIES)],
            'tag' => ['nullable', 'integer', Rule::exists('tags', 'id')],
        ]);

        $issues = Issue::query()
            ->with(['project', 'tags'])
            ->when($filters['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
            ->when($filters['priority'] ?? null, fn ($query, $priority) => $query->where('priority', $priority))
            ->when($filters['tag'] ?? null, fn ($query, $tagId) => $query->whereHas('tags', fn ($tagQuery) => $tagQuery->whereKey($tagId)))
            ->orderByRaw("case status when 'open' then 1 when 'in_progress' then 2 when 'closed' then 3 else 4 end")
            ->orderByRaw('case when due_date is null then 1 else 0 end')
            ->orderBy('due_date')
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('issues.index', [
            'issues' => $issues,
            'tags' => Tag::query()->orderBy('name')->get(),
            'filters' => $filters,
        ]);
    }

    public function create(Request $request): View
    {
        return view('issues.create', [
            'issue' => new Issue([
                'project_id' => $request->integer('project') ?: null,
                'status' => 'open',
                'priority' => 'medium',
            ]),
            'projects' => Project::query()->orderBy('name')->get(),
            'tags' => Tag::query()->orderBy('name')->get(),
            'selectedTagIds' => [],
        ]);
    }

    public function store(StoreIssueRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $tagIds = $validated['tag_ids'] ?? [];

        unset($validated['tag_ids']);

        $issue = Issue::create($validated);
        $issue->tags()->sync($tagIds);

        return redirect()
            ->route('issues.show', $issue)
            ->with('success', 'Issue created successfully.');
    }

    public function show(Issue $issue): View
    {
        $issue->load(['project', 'tags']);
        $issue->loadCount('comments');

        return view('issues.show', [
            'issue' => $issue,
            'availableTags' => Tag::query()
                ->whereNotIn('id', $issue->tags->pluck('id'))
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function edit(Issue $issue): View
    {
        $issue->load('tags');

        return view('issues.edit', [
            'issue' => $issue,
            'projects' => Project::query()->orderBy('name')->get(),
            'tags' => Tag::query()->orderBy('name')->get(),
            'selectedTagIds' => $issue->tags->pluck('id')->all(),
        ]);
    }

    public function update(UpdateIssueRequest $request, Issue $issue): RedirectResponse
    {
        $validated = $request->validated();
        $tagIds = $validated['tag_ids'] ?? [];

        unset($validated['tag_ids']);

        $issue->update($validated);
        $issue->tags()->sync($tagIds);

        return redirect()
            ->route('issues.show', $issue)
            ->with('success', 'Issue updated successfully.');
    }

    public function destroy(Issue $issue): RedirectResponse
    {
        $issue->delete();

        return redirect()
            ->route('issues.index')
            ->with('success', 'Issue deleted successfully.');
    }
}
