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

    <section class="mb-8 rounded-lg border border-slate-200 bg-white p-6 shadow-sm" data-issue-tags>
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-lg font-semibold text-slate-950">Manage tags</h2>
                <p class="mt-1 text-sm text-slate-600">Attach or remove existing tags without leaving this page.</p>
            </div>
            <a href="{{ route('tags.index') }}" class="text-sm font-medium text-sky-700 hover:text-sky-800">Manage tag library</a>
        </div>

        <div data-tag-error class="mt-4 hidden rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700"></div>

        <div class="mt-5">
            <h3 class="text-sm font-semibold uppercase tracking-wide text-slate-500">Current tags</h3>
            <div data-attached-tags class="mt-3 flex flex-wrap gap-2">
                <p data-empty-attached class="{{ $issue->tags->isEmpty() ? '' : 'hidden' }} text-sm text-slate-600">This issue has no tags.</p>
            @forelse ($issue->tags as $tag)
                <span data-attached-tag data-tag-id="{{ $tag->id }}" class="inline-flex items-center gap-2 rounded-full bg-slate-100 px-3 py-1.5 text-sm font-medium text-slate-700">
                    <span class="h-2.5 w-2.5 rounded-full" style="background-color: {{ $tag->color ?? '#64748b' }}"></span>
                    <span>{{ $tag->name }}</span>
                    <button
                        type="button"
                        data-tag-action="detach"
                        data-url="{{ route('issues.tags.detach', [$issue, $tag]) }}"
                        data-tag-name="{{ $tag->name }}"
                        data-tag-color="{{ $tag->color }}"
                        class="ml-1 rounded-full px-1 text-slate-500 hover:bg-slate-200 hover:text-slate-900"
                        aria-label="Remove {{ $tag->name }} tag"
                    >
                        &times;
                    </button>
                </span>
            @endforelse
            </div>
        </div>

        <div class="mt-6 border-t border-slate-200 pt-5">
            <h3 class="text-sm font-semibold uppercase tracking-wide text-slate-500">Available tags</h3>
            <div data-available-tags class="mt-3 flex flex-wrap gap-2">
                @php
                    $availableTags = $availableTags ?? collect();
                @endphp

                <p data-empty-available class="{{ $availableTags->isEmpty() ? '' : 'hidden' }} text-sm text-slate-600">All tags are attached to this issue.</p>
                @foreach ($availableTags as $tag)
                    <button
                        type="button"
                        data-tag-id="{{ $tag->id }}"
                        data-tag-action="attach"
                        data-url="{{ route('issues.tags.attach', [$issue, $tag]) }}"
                        data-tag-name="{{ $tag->name }}"
                        data-tag-color="{{ $tag->color }}"
                        class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-3 py-1.5 text-sm font-medium text-slate-700 hover:bg-slate-50"
                        aria-label="Attach {{ $tag->name }} tag"
                    >
                        <span class="h-2.5 w-2.5 rounded-full" style="background-color: {{ $tag->color ?? '#64748b' }}"></span>
                        <span>{{ $tag->name }}</span>
                        <span class="text-sky-700">Add</span>
                    </button>
                @endforeach
            </div>
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

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const manager = document.querySelector('[data-issue-tags]');

            if (!manager) {
                return;
            }

            const attachedList = manager.querySelector('[data-attached-tags]');
            const availableList = manager.querySelector('[data-available-tags]');
            const attachedEmpty = manager.querySelector('[data-empty-attached]');
            const availableEmpty = manager.querySelector('[data-empty-available]');
            const errorBox = manager.querySelector('[data-tag-error]');
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            const showError = (message) => {
                errorBox.textContent = message;
                errorBox.classList.remove('hidden');
            };

            const clearError = () => {
                errorBox.textContent = '';
                errorBox.classList.add('hidden');
            };

            const toggleEmptyStates = () => {
                const attachedCount = attachedList.querySelectorAll('[data-attached-tag]').length;
                const availableCount = availableList.querySelectorAll('[data-tag-id]').length;

                attachedEmpty.classList.toggle('hidden', attachedCount > 0);
                availableEmpty.classList.toggle('hidden', availableCount > 0);
            };

            const tagColor = (tag) => tag.color || '#64748b';

            const createAttachedTag = (tag, detachUrl) => {
                const wrapper = document.createElement('span');
                wrapper.dataset.attachedTag = '';
                wrapper.dataset.tagId = tag.id;
                wrapper.className = 'inline-flex items-center gap-2 rounded-full bg-slate-100 px-3 py-1.5 text-sm font-medium text-slate-700';
                wrapper.innerHTML = `
                    <span class="h-2.5 w-2.5 rounded-full" style="background-color: ${tagColor(tag)}"></span>
                    <span></span>
                    <button type="button" data-tag-action="detach" data-url="${detachUrl}" class="ml-1 rounded-full px-1 text-slate-500 hover:bg-slate-200 hover:text-slate-900"></button>
                `;
                wrapper.querySelector('span:nth-child(2)').textContent = tag.name;
                const button = wrapper.querySelector('button');
                button.dataset.tagName = tag.name;
                button.dataset.tagColor = tag.color || '';
                button.setAttribute('aria-label', `Remove ${tag.name} tag`);
                button.innerHTML = '&times;';

                return wrapper;
            };

            const createAvailableTag = (tag, attachUrl) => {
                const button = document.createElement('button');
                button.type = 'button';
                button.dataset.tagId = tag.id;
                button.dataset.tagAction = 'attach';
                button.dataset.url = attachUrl;
                button.dataset.tagName = tag.name;
                button.dataset.tagColor = tag.color || '';
                button.className = 'inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-3 py-1.5 text-sm font-medium text-slate-700 hover:bg-slate-50';
                button.setAttribute('aria-label', `Attach ${tag.name} tag`);
                button.innerHTML = `
                    <span class="h-2.5 w-2.5 rounded-full" style="background-color: ${tagColor(tag)}"></span>
                    <span></span>
                    <span class="text-sky-700">Add</span>
                `;
                button.querySelector('span:nth-child(2)').textContent = tag.name;

                return button;
            };

            manager.addEventListener('click', async (event) => {
                const button = event.target.closest('[data-tag-action]');

                if (!button || !manager.contains(button)) {
                    return;
                }

                clearError();
                button.disabled = true;
                button.classList.add('opacity-60');

                const action = button.dataset.tagAction;
                const method = action === 'attach' ? 'POST' : 'DELETE';

                try {
                    const response = await fetch(button.dataset.url, {
                        method,
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                    });

                    const data = await response.json();

                    if (!response.ok) {
                        const message = data.message || Object.values(data.errors || {}).flat().join(' ') || 'Unable to update tags.';
                        throw new Error(message);
                    }

                    const tag = data.tag;

                    if (action === 'attach') {
                        attachedList.appendChild(createAttachedTag(tag, button.dataset.url));
                        button.remove();
                    } else {
                        availableList.appendChild(createAvailableTag(tag, button.dataset.url));
                        button.closest('[data-attached-tag]').remove();
                    }

                    toggleEmptyStates();
                } catch (error) {
                    showError(error.message);
                    button.disabled = false;
                    button.classList.remove('opacity-60');
                }
            });
        });
    </script>
@endpush
