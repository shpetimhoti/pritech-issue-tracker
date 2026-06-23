@extends('layouts.app')

@section('content')
    @php
        $statusClasses = [
            'open' => 'bg-sky-50 text-sky-700',
            'in_progress' => 'bg-amber-50 text-amber-700',
            'closed' => 'bg-emerald-50 text-emerald-700',
        ];

        $priorityClasses = [
            'low' => 'bg-slate-100 text-slate-700',
            'medium' => 'bg-violet-50 text-violet-700',
            'high' => 'bg-red-50 text-red-700',
        ];
    @endphp

    <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <a href="{{ route('projects.index') }}" class="text-sm font-medium text-sky-700 hover:text-sky-800">Back to projects</a>
            <h1 class="mt-3 text-3xl font-semibold tracking-tight text-slate-950">{{ $project->name }}</h1>
            <p class="mt-3 max-w-3xl text-sm leading-6 text-slate-700">{{ $project->description }}</p>
        </div>

        <div class="flex gap-2">
            <a href="{{ route('issues.create', ['project' => $project->id]) }}" class="inline-flex items-center justify-center rounded-md bg-sky-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-sky-700">
                New issue
            </a>
            @can('update', $project)
                <a href="{{ route('projects.edit', $project) }}" class="inline-flex items-center justify-center rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50">
                    Edit
                </a>
            @endcan
            @can('delete', $project)
                <form action="{{ route('projects.destroy', $project) }}" method="POST" onsubmit="return confirm('Delete this project and its dependent issues?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center justify-center rounded-md bg-red-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-700">
                        Delete
                    </button>
                </form>
            @endcan
        </div>
    </div>

    <section class="mb-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Owner</p>
            <p class="mt-2 text-sm font-medium text-slate-950">{{ $project->owner?->name ?? 'Unassigned' }}</p>
        </div>
        <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Start date</p>
            <p class="mt-2 text-sm font-medium text-slate-950">{{ $project->start_date?->format('M j, Y') ?? 'Not set' }}</p>
        </div>
        <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Deadline</p>
            <p class="mt-2 text-sm font-medium text-slate-950">{{ $project->deadline?->format('M j, Y') ?? 'Not set' }}</p>
        </div>
        <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Issues</p>
            <p class="mt-2 text-sm font-medium text-slate-950">{{ $project->issues_count }} {{ Str::plural('issue', $project->issues_count) }}</p>
        </div>
    </section>

    <section>
        <div class="mb-4 flex items-center justify-between">
            <h2 class="text-xl font-semibold tracking-tight text-slate-950">Issues</h2>
            <a href="{{ route('issues.create', ['project' => $project->id]) }}" class="text-sm font-medium text-sky-700 hover:text-sky-800">New issue</a>
        </div>

        @if ($project->issues->isEmpty())
            <div class="rounded-lg border border-dashed border-slate-300 bg-white px-6 py-10 text-center">
                <h3 class="text-base font-semibold text-slate-950">No issues yet</h3>
                <p class="mt-2 text-sm text-slate-600">Seeded or future issues for this project will appear here.</p>
            </div>
        @else
            <div class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Title</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Status</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Priority</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Due date</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            @foreach ($project->issues as $issue)
                                <tr>
                                    <td class="max-w-md px-4 py-4 text-sm font-medium">
                                        <a href="{{ route('issues.show', $issue) }}" class="text-slate-950 hover:text-sky-700">{{ $issue->title }}</a>
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-4">
                                        <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $statusClasses[$issue->status] ?? 'bg-slate-100 text-slate-700' }}">
                                            {{ Str::headline($issue->status) }}
                                        </span>
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-4">
                                        <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $priorityClasses[$issue->priority] ?? 'bg-slate-100 text-slate-700' }}">
                                            {{ Str::headline($issue->priority) }}
                                        </span>
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-4 text-sm text-slate-700">{{ $issue->due_date?->format('M j, Y') ?? 'No due date' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </section>
@endsection
