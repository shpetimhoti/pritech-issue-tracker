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
            <a href="{{ route('issues.index') }}" class="text-sm font-medium text-sky-700 hover:text-sky-800">Back to issues</a>
            <h1 class="mt-3 text-3xl font-semibold tracking-tight text-slate-950">{{ $issue->title }}</h1>
            <p class="mt-3 max-w-3xl text-sm leading-6 text-slate-700">{{ $issue->description }}</p>
        </div>

        <div class="flex gap-2">
            <a href="{{ route('issues.edit', $issue) }}" class="inline-flex items-center justify-center rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50">
                Edit
            </a>
            <form action="{{ route('issues.destroy', $issue) }}" method="POST" onsubmit="return confirm('Delete this issue?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="inline-flex items-center justify-center rounded-md bg-red-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-700">
                    Delete
                </button>
            </form>
        </div>
    </div>

    <section class="mb-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Project</p>
            <a href="{{ route('projects.show', $issue->project) }}" class="mt-2 block text-sm font-medium text-sky-700 hover:text-sky-800">{{ $issue->project->name }}</a>
        </div>
        <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Status</p>
            <span class="mt-2 inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $statusClasses[$issue->status] ?? 'bg-slate-100 text-slate-700' }}">{{ Str::headline($issue->status) }}</span>
        </div>
        <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Priority</p>
            <span class="mt-2 inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $priorityClasses[$issue->priority] ?? 'bg-slate-100 text-slate-700' }}">{{ Str::headline($issue->priority) }}</span>
        </div>
        <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Due date</p>
            <p class="mt-2 text-sm font-medium text-slate-950">{{ $issue->due_date?->format('M j, Y') ?? 'No due date' }}</p>
        </div>
    </section>

    <section class="mb-8 rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
        <h2 class="text-lg font-semibold text-slate-950">Tags</h2>
        <div class="mt-4 flex flex-wrap gap-2">
            @forelse ($issue->tags as $tag)
                <span class="inline-flex items-center gap-2 rounded-full bg-slate-100 px-3 py-1.5 text-sm font-medium text-slate-700">
                    <span class="h-2.5 w-2.5 rounded-full" style="background-color: {{ $tag->color ?? '#64748b' }}"></span>
                    {{ $tag->name }}
                </span>
            @empty
                <p class="text-sm text-slate-600">No tags selected.</p>
            @endforelse
        </div>
    </section>

    <section class="rounded-lg border border-dashed border-slate-300 bg-white p-6">
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-semibold text-slate-950">Comments</h2>
            <span class="text-sm text-slate-500">{{ $issue->comments_count }} {{ Str::plural('comment', $issue->comments_count) }}</span>
        </div>
        <p class="mt-3 text-sm text-slate-600">Comments will load here.</p>
    </section>
@endsection
