@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-3xl">
        <div class="mb-8">
            <a href="{{ route('projects.show', $project) }}" class="text-sm font-medium text-sky-700 hover:text-sky-800">Back to project</a>
            <h1 class="mt-3 text-3xl font-semibold tracking-tight text-slate-950">Edit project</h1>
            <p class="mt-2 text-sm text-slate-600">Update project details and timeline.</p>
        </div>

        <form action="{{ route('projects.update', $project) }}" method="POST" class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
            @include('projects._form', [
                'method' => 'PUT',
                'submitLabel' => 'Save changes',
                'cancelUrl' => route('projects.show', $project),
            ])
        </form>
    </div>
@endsection
