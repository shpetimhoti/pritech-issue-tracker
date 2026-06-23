@extends('layouts.app')

@section('content')
    <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-3xl font-semibold tracking-tight text-slate-950">Issues</h1>
            <p class="mt-2 text-sm text-slate-600">Review and filter work across all projects.</p>
        </div>

        <a href="{{ route('issues.create') }}" class="inline-flex items-center justify-center rounded-md bg-sky-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-sky-700">
            New issue
        </a>
    </div>

    <form action="{{ route('issues.index') }}" method="GET" class="mb-6 rounded-lg border border-slate-200 bg-white p-4 shadow-sm" data-issue-search-form>
        <div class="grid gap-4 md:grid-cols-5">
            <div class="md:col-span-2">
                <label for="search" class="block text-xs font-semibold uppercase tracking-wide text-slate-500">Search</label>
                <div class="mt-2 flex rounded-md border border-slate-300 bg-white shadow-sm focus-within:border-sky-500 focus-within:ring-2 focus-within:ring-sky-200">
                    <input
                        id="search"
                        name="search"
                        type="search"
                        value="{{ $filters['search'] ?? '' }}"
                        placeholder="Search issues..."
                        class="block w-full rounded-l-md border-0 bg-transparent px-3 py-2 text-sm outline-none"
                        data-issue-search-input
                    >
                    <button
                        type="button"
                        class="{{ empty($filters['search']) ? 'hidden' : '' }} px-3 text-sm font-medium text-slate-500 hover:text-slate-900"
                        data-clear-issue-search
                        aria-label="Clear issue search"
                    >
                        Clear
                    </button>
                </div>
            </div>

            <div>
                <label for="status" class="block text-xs font-semibold uppercase tracking-wide text-slate-500">Status</label>
                <select id="status" name="status" class="mt-2 block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm" data-issue-filter>
                    <option value="">All statuses</option>
                    @foreach (App\Models\Issue::STATUSES as $status)
                        <option value="{{ $status }}" @selected(($filters['status'] ?? '') === $status)>{{ Str::headline($status) }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="priority" class="block text-xs font-semibold uppercase tracking-wide text-slate-500">Priority</label>
                <select id="priority" name="priority" class="mt-2 block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm" data-issue-filter>
                    <option value="">All priorities</option>
                    @foreach (App\Models\Issue::PRIORITIES as $priority)
                        <option value="{{ $priority }}" @selected(($filters['priority'] ?? '') === $priority)>{{ Str::headline($priority) }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="tag" class="block text-xs font-semibold uppercase tracking-wide text-slate-500">Tag</label>
                <select id="tag" name="tag" class="mt-2 block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm" data-issue-filter>
                    <option value="">All tags</option>
                    @foreach ($tags as $tag)
                        <option value="{{ $tag->id }}" @selected((int) ($filters['tag'] ?? 0) === $tag->id)>{{ $tag->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="mt-4 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3">
                <p class="hidden text-sm text-slate-600" data-issue-search-loading>Searching...</p>
                <p class="hidden text-sm text-red-600" data-issue-search-error></p>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="inline-flex justify-center rounded-md bg-sky-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-sky-700">
                    Apply filters
                </button>
                <a href="{{ route('issues.index') }}" class="inline-flex justify-center rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50" data-clear-all-issue-filters>
                    Clear
                </a>
            </div>
        </div>
    </form>

    <div data-issue-results>
        @include('issues._results', ['issues' => $issues, 'tags' => $tags, 'filters' => $filters])
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.querySelector('[data-issue-search-form]');

            if (!form) {
                return;
            }

            const searchInput = form.querySelector('[data-issue-search-input]');
            const clearSearchButton = form.querySelector('[data-clear-issue-search]');
            const clearAllLink = form.querySelector('[data-clear-all-issue-filters]');
            const loading = form.querySelector('[data-issue-search-loading]');
            const error = form.querySelector('[data-issue-search-error]');
            const results = document.querySelector('[data-issue-results]');
            let debounceTimer = null;
            let controller = null;

            const setLoading = (isLoading) => {
                loading.classList.toggle('hidden', !isLoading);
            };

            const setError = (message = '') => {
                error.textContent = message;
                error.classList.toggle('hidden', message === '');
            };

            const toggleClearSearch = () => {
                clearSearchButton.classList.toggle('hidden', searchInput.value.trim() === '');
            };

            const buildUrl = (url = form.action) => {
                const target = new URL(url, window.location.origin);
                const formData = new FormData(form);
                const page = target.searchParams.get('page');

                target.search = '';

                if (page) {
                    target.searchParams.set('page', page);
                }

                formData.forEach((value, key) => {
                    const trimmed = String(value).trim();

                    if (trimmed !== '') {
                        target.searchParams.set(key, trimmed);
                    }
                });

                return target;
            };

            const syncFormFromUrl = (url) => {
                const target = new URL(url, window.location.origin);

                ['search', 'status', 'priority', 'tag'].forEach((field) => {
                    const input = form.elements[field];

                    if (input) {
                        input.value = target.searchParams.get(field) || '';
                    }
                });

                toggleClearSearch();
            };

            const loadIssues = async (url = form.action, pushState = true) => {
                if (controller) {
                    controller.abort();
                }

                const activeController = new AbortController();
                controller = activeController;
                const target = buildUrl(url);

                setLoading(true);
                setError();

                try {
                    const response = await fetch(target, {
                        headers: {
                            'Accept': 'text/html',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        signal: activeController.signal,
                    });

                    if (!response.ok) {
                        throw new Error('Unable to load issues.');
                    }

                    results.innerHTML = await response.text();

                    if (pushState) {
                        window.history.replaceState({}, '', target);
                    }
                } catch (fetchError) {
                    if (fetchError.name !== 'AbortError') {
                        setError(fetchError.message);
                    }
                } finally {
                    if (controller === activeController && !activeController.signal.aborted) {
                        setLoading(false);
                    }
                }
            };

            const debouncedLoad = () => {
                window.clearTimeout(debounceTimer);
                debounceTimer = window.setTimeout(() => loadIssues(), 350);
            };

            form.addEventListener('submit', (event) => {
                event.preventDefault();
                window.clearTimeout(debounceTimer);
                loadIssues();
            });

            searchInput.addEventListener('input', () => {
                toggleClearSearch();
                debouncedLoad();
            });

            form.querySelectorAll('[data-issue-filter]').forEach((filter) => {
                filter.addEventListener('change', () => loadIssues());
            });

            clearSearchButton.addEventListener('click', () => {
                searchInput.value = '';
                toggleClearSearch();
                loadIssues();
            });

            clearAllLink.addEventListener('click', (event) => {
                event.preventDefault();
                form.reset();
                searchInput.value = '';
                toggleClearSearch();
                loadIssues(clearAllLink.href);
            });

            results.addEventListener('click', (event) => {
                const link = event.target.closest('[data-issue-pagination] a');

                if (!link) {
                    return;
                }

                event.preventDefault();
                syncFormFromUrl(link.href);
                loadIssues(link.href);
            });

            toggleClearSearch();
        });
    </script>
@endpush
