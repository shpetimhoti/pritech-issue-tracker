@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-3xl">
        <div class="mb-8">
            <a href="{{ route('issues.show', $issue) }}" class="text-sm font-medium text-sky-700 hover:text-sky-800">Back to issue</a>
            <h1 class="mt-3 text-3xl font-semibold tracking-tight text-slate-950">Edit issue</h1>
            <p class="mt-2 text-sm text-slate-600">Update issue details, priority, due date, and tags.</p>
        </div>

        <form action="{{ route('issues.update', $issue) }}" method="POST" class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
            @include('issues._form', [
                'method' => 'PUT',
                'submitLabel' => 'Save changes',
                'cancelUrl' => route('issues.show', $issue),
            ])
        </form>
    </div>
@endsection
