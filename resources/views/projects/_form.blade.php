@csrf

@if ($method !== 'POST')
    @method($method)
@endif

<div class="space-y-6">
    <div>
        <label for="name" class="block text-sm font-medium text-slate-800">Project name</label>
        <input
            id="name"
            name="name"
            type="text"
            value="{{ old('name', $project->name) }}"
            class="mt-2 block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm shadow-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-200"
            required
            maxlength="120"
        >
        <x-form-error field="name" />
    </div>

    <div>
        <label for="description" class="block text-sm font-medium text-slate-800">Description</label>
        <textarea
            id="description"
            name="description"
            rows="6"
            class="mt-2 block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm shadow-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-200"
            required
            maxlength="3000"
        >{{ old('description', $project->description) }}</textarea>
        <x-form-error field="description" />
    </div>

    <div class="grid gap-6 sm:grid-cols-2">
        <div>
            <label for="start_date" class="block text-sm font-medium text-slate-800">Start date</label>
            <input
                id="start_date"
                name="start_date"
                type="date"
                value="{{ old('start_date', optional($project->start_date)->format('Y-m-d')) }}"
                class="mt-2 block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm shadow-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-200"
            >
            <x-form-error field="start_date" />
        </div>

        <div>
            <label for="deadline" class="block text-sm font-medium text-slate-800">Deadline</label>
            <input
                id="deadline"
                name="deadline"
                type="date"
                value="{{ old('deadline', optional($project->deadline)->format('Y-m-d')) }}"
                class="mt-2 block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm shadow-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-200"
            >
            <x-form-error field="deadline" />
        </div>
    </div>

    <div class="flex flex-col-reverse gap-3 border-t border-slate-200 pt-6 sm:flex-row sm:justify-end">
        <a href="{{ $cancelUrl }}" class="inline-flex justify-center rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50">
            Cancel
        </a>
        <button type="submit" class="inline-flex justify-center rounded-md bg-sky-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-sky-300">
            {{ $submitLabel }}
        </button>
    </div>
</div>
