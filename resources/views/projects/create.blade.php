@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-3xl">
        <div class="mb-8">
            <a href="{{ route('projects.index') }}" class="text-sm font-medium text-sky-700 hover:text-sky-800">Back to projects</a>
            <h1 class="mt-3 text-3xl font-semibold tracking-tight text-slate-950">New project</h1>
            <p class="mt-2 text-sm text-slate-600">Capture the project details before adding issues in a later step.</p>
        </div>

        <form action="{{ route('projects.store') }}" method="POST" class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
            @include('projects._form', [
                'method' => 'POST',
                'submitLabel' => 'Create project',
                'cancelUrl' => route('projects.index'),
            ])
        </form>
    </div>
@endsection
