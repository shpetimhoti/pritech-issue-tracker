@extends('layouts.app')

@section('content')
    <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-3xl font-semibold tracking-tight text-slate-950">Projects</h1>
            <p class="mt-2 text-sm text-slate-600">Manage active work and review issue volume by project.</p>
        </div>

        @auth
            <a href="{{ route('projects.create') }}" class="inline-flex items-center justify-center rounded-md bg-sky-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-sky-700">
                New project
            </a>
        @else
            <a href="{{ route('login') }}" class="inline-flex items-center justify-center rounded-md bg-sky-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-sky-700">
                Login to create projects
            </a>
        @endauth
    </div>

    @if ($projects->isEmpty())
        <div class="rounded-lg border border-dashed border-slate-300 bg-white px-6 py-12 text-center">
            <h2 class="text-lg font-semibold text-slate-950">No projects yet</h2>
            <p class="mt-2 text-sm text-slate-600">Create the first project to start organizing issues.</p>
            @auth
                <a href="{{ route('projects.create') }}" class="mt-6 inline-flex items-center justify-center rounded-md bg-sky-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-sky-700">
                    New project
                </a>
            @else
                <a href="{{ route('login') }}" class="mt-6 inline-flex items-center justify-center rounded-md bg-sky-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-sky-700">
                    Login to create projects
                </a>
            @endauth
        </div>
    @else
        <div class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Project</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Dates</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Issues</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Deadline</th>
                            <th scope="col" class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 bg-white">
                        @foreach ($projects as $project)
                            @php
                                $deadlineStatus = 'No deadline';
                                $deadlineClass = 'bg-slate-100 text-slate-700';

                                if ($project->deadline) {
                                    $deadlineStatus = $project->deadline->isPast() && ! $project->deadline->isToday() ? 'Overdue' : 'Scheduled';
                                    $deadlineClass = $deadlineStatus === 'Overdue' ? 'bg-red-50 text-red-700' : 'bg-emerald-50 text-emerald-700';
                                }
                            @endphp
                            <tr>
                                <td class="max-w-md px-4 py-4 align-top">
                                    <a href="{{ route('projects.show', $project) }}" class="font-semibold text-slate-950 hover:text-sky-700">
                                        {{ $project->name }}
                                    </a>
                                    <p class="mt-1 text-xs text-slate-500">Owner: {{ $project->owner?->name ?? 'Unassigned' }}</p>
                                    <p class="mt-1 text-sm text-slate-600">{{ Str::limit($project->description, 130) }}</p>
                                </td>
                                <td class="whitespace-nowrap px-4 py-4 align-top text-sm text-slate-700">
                                    {{ $project->start_date?->format('M j, Y') ?? 'Not set' }}
                                    <span class="mx-1 text-slate-400">to</span>
                                    {{ $project->deadline?->format('M j, Y') ?? 'Not set' }}
                                </td>
                                <td class="whitespace-nowrap px-4 py-4 align-top text-sm text-slate-700">
                                    {{ $project->issues_count }} {{ Str::plural('issue', $project->issues_count) }}
                                </td>
                                <td class="whitespace-nowrap px-4 py-4 align-top">
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $deadlineClass }}">
                                        {{ $deadlineStatus }}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-4 py-4 align-top text-right text-sm">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('projects.show', $project) }}" class="rounded-md px-3 py-1.5 font-medium text-slate-700 hover:bg-slate-100">View</a>
                                        @can('update', $project)
                                            <a href="{{ route('projects.edit', $project) }}" class="rounded-md px-3 py-1.5 font-medium text-sky-700 hover:bg-sky-50">Edit</a>
                                        @endcan
                                        @can('delete', $project)
                                            <form action="{{ route('projects.destroy', $project) }}" method="POST" onsubmit="return confirm('Delete this project and its dependent issues?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="rounded-md px-3 py-1.5 font-medium text-red-700 hover:bg-red-50">
                                                    Delete
                                                </button>
                                            </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-6">
            {{ $projects->links() }}
        </div>
    @endif
@endsection
