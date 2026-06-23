<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? 'Trackly' }}</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-slate-50 font-sans text-slate-900 antialiased">
        <header class="border-b border-slate-200 bg-white">
            <div class="mx-auto flex max-w-6xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
                <a href="{{ route('projects.index') }}" class="text-xl font-semibold tracking-tight text-slate-950">
                    Trackly
                </a>

                <nav class="flex items-center gap-1" aria-label="Main navigation">
                    <a href="{{ route('projects.index') }}" class="rounded-md px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100 hover:text-slate-950">
                        Projects
                    </a>
                    <a href="{{ route('issues.index') }}" class="rounded-md px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100 hover:text-slate-950">
                        Issues
                    </a>
                    <a href="{{ route('tags.index') }}" class="rounded-md px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100 hover:text-slate-950">
                        Tags
                    </a>
                </nav>
            </div>
        </header>

        <main class="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8">
            <x-flash-message />

            {{ $slot ?? '' }}
            @yield('content')
        </main>

        @stack('scripts')
    </body>
</html>
