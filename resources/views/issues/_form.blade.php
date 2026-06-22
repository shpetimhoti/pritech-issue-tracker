@csrf

@if ($method !== 'POST')
    @method($method)
@endif

@php
    $selectedTagIds = collect(old('tag_ids', $selectedTagIds ?? []))->map(fn ($id) => (int) $id)->all();
@endphp

<div class="space-y-6">
    <div>
        <label for="project_id" class="block text-sm font-medium text-slate-800">Project</label>
        <select
            id="project_id"
            name="project_id"
            class="mt-2 block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm shadow-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-200"
            required
        >
            <option value="">Choose a project</option>
            @foreach ($projects as $project)
                <option value="{{ $project->id }}" @selected((int) old('project_id', $issue->project_id) === $project->id)>
                    {{ $project->name }}
                </option>
            @endforeach
        </select>
        <x-form-error field="project_id" />
    </div>

    <div>
        <label for="title" class="block text-sm font-medium text-slate-800">Title</label>
        <input
            id="title"
            name="title"
            type="text"
            value="{{ old('title', $issue->title) }}"
            class="mt-2 block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm shadow-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-200"
            required
            maxlength="180"
        >
        <x-form-error field="title" />
    </div>

    <div>
        <label for="description" class="block text-sm font-medium text-slate-800">Description</label>
        <textarea
            id="description"
            name="description"
            rows="7"
            class="mt-2 block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm shadow-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-200"
            required
            maxlength="5000"
        >{{ old('description', $issue->description) }}</textarea>
        <x-form-error field="description" />
    </div>

    <div class="grid gap-6 sm:grid-cols-3">
        <div>
            <label for="status" class="block text-sm font-medium text-slate-800">Status</label>
            <select id="status" name="status" class="mt-2 block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm shadow-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-200" required>
                @foreach (App\Models\Issue::STATUSES as $status)
                    <option value="{{ $status }}" @selected(old('status', $issue->status ?? 'open') === $status)>
                        {{ Str::headline($status) }}
                    </option>
                @endforeach
            </select>
            <x-form-error field="status" />
        </div>

        <div>
            <label for="priority" class="block text-sm font-medium text-slate-800">Priority</label>
            <select id="priority" name="priority" class="mt-2 block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm shadow-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-200" required>
                @foreach (App\Models\Issue::PRIORITIES as $priority)
                    <option value="{{ $priority }}" @selected(old('priority', $issue->priority ?? 'medium') === $priority)>
                        {{ Str::headline($priority) }}
                    </option>
                @endforeach
            </select>
            <x-form-error field="priority" />
        </div>

        <div>
            <label for="due_date" class="block text-sm font-medium text-slate-800">Due date</label>
            <input
                id="due_date"
                name="due_date"
                type="date"
                value="{{ old('due_date', optional($issue->due_date)->format('Y-m-d')) }}"
                class="mt-2 block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm shadow-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-200"
            >
            <x-form-error field="due_date" />
        </div>
    </div>

    <fieldset>
        <legend class="text-sm font-medium text-slate-800">Tags</legend>
        <div class="mt-3 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
            @forelse ($tags as $tag)
                <label class="flex items-center gap-3 rounded-md border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700">
                    <input
                        type="checkbox"
                        name="tag_ids[]"
                        value="{{ $tag->id }}"
                        @checked(in_array($tag->id, $selectedTagIds, true))
                        class="rounded border-slate-300 text-sky-600 focus:ring-sky-500"
                    >
                    <span class="inline-flex h-2.5 w-2.5 rounded-full" style="background-color: {{ $tag->color ?? '#64748b' }}"></span>
                    <span>{{ $tag->name }}</span>
                </label>
            @empty
                <p class="text-sm text-slate-500">No tags are available yet.</p>
            @endforelse
        </div>
        <x-form-error field="tag_ids" />
        <x-form-error field="tag_ids.*" />
    </fieldset>

    <div class="flex flex-col-reverse gap-3 border-t border-slate-200 pt-6 sm:flex-row sm:justify-end">
        <a href="{{ $cancelUrl }}" class="inline-flex justify-center rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50">
            Cancel
        </a>
        <button type="submit" class="inline-flex justify-center rounded-md bg-sky-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-sky-300">
            {{ $submitLabel }}
        </button>
    </div>
</div>
