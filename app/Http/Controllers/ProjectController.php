<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ProjectController extends Controller
{
    public function index(): View
    {
        $projects = Project::query()
            ->with('owner')
            ->withCount('issues')
            ->latest()
            ->paginate(10);

        return view('projects.index', ['projects' => $projects]);
    }

    public function create(): View
    {
        Gate::authorize('create', Project::class);

        return view('projects.create', ['project' => new Project()]);
    }

    public function store(StoreProjectRequest $request): RedirectResponse
    {
        Gate::authorize('create', Project::class);

        $project = Project::create([
            ...$request->validated(),
            'user_id' => $request->user()->id,
        ]);

        return redirect()
            ->route('projects.show', $project)
            ->with('success', 'Project created successfully.');
    }

    public function show(Project $project): View
    {
        $project->loadCount('issues');
        $project->load([
            'owner',
            'issues' => fn ($query) => $query
                ->orderByRaw("case status when 'open' then 1 when 'in_progress' then 2 when 'closed' then 3 else 4 end")
                ->orderByRaw('case when due_date is null then 1 else 0 end')
                ->orderBy('due_date')
                ->latest(),
        ]);

        return view('projects.show', ['project' => $project]);
    }

    public function edit(Project $project): View
    {
        Gate::authorize('update', $project);

        return view('projects.edit', ['project' => $project]);
    }

    public function update(UpdateProjectRequest $request, Project $project): RedirectResponse
    {
        Gate::authorize('update', $project);

        $project->update($request->validated());

        return redirect()
            ->route('projects.show', $project)
            ->with('success', 'Project updated successfully.');
    }

    public function destroy(Project $project): RedirectResponse
    {
        Gate::authorize('delete', $project);

        $project->delete();

        return redirect()
            ->route('projects.index')
            ->with('success', 'Project deleted successfully.');
    }
}
