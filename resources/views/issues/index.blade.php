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

        $activeFilters = collect([
            'Status' => isset($filters['status']) ? Str::headline($filters['status']) : null,
            'Priority' => isset($filters['priority']) ? Str::headline($filters['priority']) : null,
            'Tag' => isset($filters['tag']) ? optional($tags->firstWhere('id', (int) $filters['tag']))->name : null,
        ])->filter();
    @endphp

    <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-3xl font-semibold tracking-tight text-slate-950">Issues</h1>
            <p class="mt-2 text-sm text-slate-600">Review and filter work across all projects.</p>
        </div>

        <a href="{{ route('issues.create') }}" class="inline-flex items-center justify-center rounded-md bg-sky-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-sky-700">
            New issue
        </a>
    </div>

    <form action="{{ route('issues.index') }}" method="GET" class="mb-6 rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
        <div class="grid gap-4 md:grid-cols-4">
            <div>
                <label for="status" class="block text-xs font-semibold uppercase tracking-wide text-slate-500">Status</label>
                <select id="status" name="status" class="mt-2 block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm">
                    <option value="">All statuses</option>
                    @foreach (App\Models\Issue::STATUSES as $status)
                        <option value="{{ $status }}" @selected(($filters['status'] ?? '') === $status)>{{ Str::headline($status) }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="priority" class="block text-xs font-semibold uppercase tracking-wide text-slate-500">Priority</label>
                <select id="priority" name="priority" class="mt-2 block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm">
                    <option value="">All priorities</option>
                    @foreach (App\Models\Issue::PRIORITIES as $priority)
                        <option value="{{ $priority }}" @selected(($filters['priority'] ?? '') === $priority)>{{ Str::headline($priority) }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="tag" class="block text-xs font-semibold uppercase tracking-wide text-slate-500">Tag</label>
                <select id="tag" name="tag" class="mt-2 block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm">
                    <option value="">All tags</option>
                    @foreach ($tags as $tag)
                        <option value="{{ $tag->id }}" @selected((int) ($filters['tag'] ?? 0) === $tag->id)>{{ $tag->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="inline-flex w-full justify-center rounded-md bg-sky-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-sky-700">
                    Apply filters
                </button>
                <a href="{{ route('issues.index') }}" class="inline-flex justify-center rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50">
                    Clear
                </a>
            </div>
        </div>
    </form>

    @if ($activeFilters->isNotEmpty())
        <div class="mb-4 flex flex-wrap gap-2 text-sm text-slate-600">
            <span class="font-medium text-slate-800">Active filters:</span>
            @foreach ($activeFilters as $label => $value)
                <span class="rounded-full bg-slate-100 px-2.5 py-1">{{ $label }}: {{ $value }}</span>
            @endforeach
        </div>
    @endif

    @if ($issues->isEmpty())
        <div class="rounded-lg border border-dashed border-slate-300 bg-white px-6 py-12 text-center">
            <h2 class="text-lg font-semibold text-slate-950">No issues found</h2>
            <p class="mt-2 text-sm text-slate-600">Create a new issue or clear the filters to see more results.</p>
            <a href="{{ route('issues.create') }}" class="mt-6 inline-flex items-center justify-center rounded-md bg-sky-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-sky-700">
                New issue
            </a>
        </div>
    @else
        <div class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Issue</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Project</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Status</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Priority</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Due date</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Tags</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @foreach ($issues as $issue)
                            <tr>
                                <td class="max-w-md px-4 py-4">
                                    <a href="{{ route('issues.show', $issue) }}" class="text-sm font-semibold text-slate-950 hover:text-sky-700">{{ $issue->title }}</a>
                                </td>
                                <td class="whitespace-nowrap px-4 py-4 text-sm text-slate-700">
                                    <a href="{{ route('projects.show', $issue->project) }}" class="hover:text-sky-700">{{ $issue->project->name }}</a>
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
                                <td class="px-4 py-4">
                                    <div class="flex max-w-xs flex-wrap gap-1.5">
                                        @forelse ($issue->tags as $tag)
                                            <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-700">{{ $tag->name }}</span>
                                        @empty
                                            <span class="text-sm text-slate-500">No tags</span>
                                        @endforelse
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-6">
            {{ $issues->links() }}
        </div>
    @endif
@endsection
