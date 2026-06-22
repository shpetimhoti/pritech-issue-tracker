@if (session('success'))
    <div class="mb-6 rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800" role="status">
        {{ session('success') }}
    </div>
@endif
