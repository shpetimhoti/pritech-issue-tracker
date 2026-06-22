<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProjectController extends Controller
{
    public function index(): View
    {
        $projects = Project::query()
            ->withCount('issues')
            ->latest()
            ->paginate(10);

        return view('projects.index', ['projects' => $projects]);
    }

    public function create(): View
    {
        return view('projects.create', ['project' => new Project()]);
    }

    public function store(StoreProjectRequest $request): RedirectResponse
    {
        $project = Project::create($request->validated());

        return redirect()
            ->route('projects.show', $project)
            ->with('success', 'Project created successfully.');
    }

    public function show(Project $project): View
    {
        $project->loadCount('issues');
        $project->load([
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
        return view('projects.edit', ['project' => $project]);
    }

    public function update(UpdateProjectRequest $request, Project $project): RedirectResponse
    {
        $project->update($request->validated());

        return redirect()
            ->route('projects.show', $project)
            ->with('success', 'Project updated successfully.');
    }

    public function destroy(Project $project): RedirectResponse
    {
        $project->delete();

        return redirect()
            ->route('projects.index')
            ->with('success', 'Project deleted successfully.');
    }
}
