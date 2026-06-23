@extends('layouts.app')

@section('content')
    <div class="mb-8">
        <h1 class="text-3xl font-semibold tracking-tight text-slate-950">Tags</h1>
        <p class="mt-2 text-sm text-slate-600">Create reusable labels for issue organization.</p>
    </div>

    <div class="grid gap-8 lg:grid-cols-[minmax(0,1fr)_minmax(0,2fr)]">
        <section class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-semibold text-slate-950">New tag</h2>

            <form action="{{ route('tags.store') }}" method="POST" class="mt-5 space-y-5">
                @csrf

                <div>
                    <label for="name" class="block text-sm font-medium text-slate-800">Name</label>
                    <input
                        id="name"
                        name="name"
                        type="text"
                        value="{{ old('name') }}"
                        class="mt-2 block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm shadow-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-200"
                        required
                        maxlength="50"
                    >
                    <x-form-error field="name" />
                </div>

                <div>
                    <label for="color" class="block text-sm font-medium text-slate-800">Color</label>
                    <input
                        id="color"
                        name="color"
                        type="text"
                        value="{{ old('color') }}"
                        placeholder="#2563eb"
                        class="mt-2 block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm shadow-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-200"
                    >
                    <x-form-error field="color" />
                </div>

                <button type="submit" class="inline-flex w-full justify-center rounded-md bg-sky-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-sky-700">
                    Create tag
                </button>
            </form>
        </section>

        <section class="rounded-lg border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-6 py-4">
                <h2 class="text-lg font-semibold text-slate-950">Existing tags</h2>
            </div>

            @if ($tags->isEmpty())
                <div class="px-6 py-10 text-center">
                    <h3 class="text-base font-semibold text-slate-950">No tags yet</h3>
                    <p class="mt-2 text-sm text-slate-600">Create a tag to make it available on issue forms.</p>
                </div>
            @else
                <div class="divide-y divide-slate-200">
                    @foreach ($tags as $tag)
                        <div class="flex items-center justify-between gap-4 px-6 py-4">
                            <div class="flex min-w-0 items-center gap-3">
                                <span class="h-3 w-3 rounded-full" style="background-color: {{ $tag->color ?? '#64748b' }}"></span>
                                <div class="min-w-0">
                                    <p class="truncate text-sm font-semibold text-slate-950">{{ $tag->name }}</p>
                                    <p class="text-xs text-slate-500">{{ $tag->color ?? 'No color set' }}</p>
                                </div>
                            </div>
                            <span class="whitespace-nowrap rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-700">
                                {{ $tag->issues_count }} {{ Str::plural('issue', $tag->issues_count) }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @endif
        </section>
    </div>
@endsection
