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
            <div class="mx-auto flex max-w-6xl flex-wrap items-center justify-between gap-3 px-4 py-4 sm:px-6 lg:px-8">
                <a href="{{ route('projects.index') }}" class="text-xl font-semibold tracking-tight text-slate-950">
                    Trackly
                </a>

                <button
                    type="button"
                    class="inline-flex items-center justify-center rounded-md border border-slate-300 bg-white px-3 py-2 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50 md:hidden"
                    aria-controls="mobile-navigation"
                    aria-expanded="false"
                    data-mobile-menu-button
                >
                    Menu
                </button>

                <nav class="hidden items-center gap-1 md:flex" aria-label="Main navigation">
                    <a href="{{ route('projects.index') }}" class="rounded-md px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100 hover:text-slate-950">
                        Projects
                    </a>
                    <a href="{{ route('issues.index') }}" class="rounded-md px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100 hover:text-slate-950">
                        Issues
                    </a>
                    <a href="{{ route('tags.index') }}" class="rounded-md px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100 hover:text-slate-950">
                        Tags
                    </a>
                    @guest
                        <a href="{{ route('login') }}" class="rounded-md px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100 hover:text-slate-950">
                            Login
                        </a>
                        <a href="{{ route('register') }}" class="rounded-md px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100 hover:text-slate-950">
                            Register
                        </a>
                    @else
                        <span class="px-3 py-2 text-sm font-medium text-slate-600">{{ auth()->user()->name }}</span>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="rounded-md px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100 hover:text-slate-950">
                                Logout
                            </button>
                        </form>
                    @endguest
                </nav>

                <nav id="mobile-navigation" class="hidden w-full border-t border-slate-200 pt-3 md:hidden" aria-label="Mobile navigation" data-mobile-menu>
                    <div class="flex flex-col gap-1">
                        <a href="{{ route('projects.index') }}" class="rounded-md px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100 hover:text-slate-950">
                            Projects
                        </a>
                        <a href="{{ route('issues.index') }}" class="rounded-md px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100 hover:text-slate-950">
                            Issues
                        </a>
                        <a href="{{ route('tags.index') }}" class="rounded-md px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100 hover:text-slate-950">
                            Tags
                        </a>
                        @guest
                            <a href="{{ route('login') }}" class="rounded-md px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100 hover:text-slate-950">
                                Login
                            </a>
                            <a href="{{ route('register') }}" class="rounded-md px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100 hover:text-slate-950">
                                Register
                            </a>
                        @else
                            <span class="px-3 py-2 text-sm font-medium text-slate-600">{{ auth()->user()->name }}</span>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full rounded-md px-3 py-2 text-left text-sm font-medium text-slate-700 hover:bg-slate-100 hover:text-slate-950">
                                    Logout
                                </button>
                            </form>
                        @endguest
                    </div>
                </nav>
            </div>
        </header>

        <main class="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8">
            <x-flash-message />

            {{ $slot ?? '' }}
            @yield('content')
        </main>

        @stack('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const button = document.querySelector('[data-mobile-menu-button]');
                const menu = document.querySelector('[data-mobile-menu]');

                if (!button || !menu) {
                    return;
                }

                button.addEventListener('click', () => {
                    const isOpen = button.getAttribute('aria-expanded') === 'true';

                    button.setAttribute('aria-expanded', String(!isOpen));
                    menu.classList.toggle('hidden', isOpen);
                });
            });
        </script>
    </body>
</html>
