@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-md">
        <div class="mb-8">
            <h1 class="text-3xl font-semibold tracking-tight text-slate-950">Register</h1>
            <p class="mt-2 text-sm text-slate-600">Create an account to own and manage projects.</p>
        </div>

        <form action="{{ route('register') }}" method="POST" class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
            @csrf

            <div class="space-y-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-slate-800">Name</label>
                    <input
                        id="name"
                        name="name"
                        type="text"
                        value="{{ old('name') }}"
                        class="mt-2 block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm shadow-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-200"
                        required
                        autofocus
                    >
                    <x-form-error field="name" />
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-slate-800">Email</label>
                    <input
                        id="email"
                        name="email"
                        type="email"
                        value="{{ old('email') }}"
                        class="mt-2 block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm shadow-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-200"
                        required
                    >
                    <x-form-error field="email" />
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-slate-800">Password</label>
                    <input
                        id="password"
                        name="password"
                        type="password"
                        class="mt-2 block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm shadow-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-200"
                        required
                    >
                    <x-form-error field="password" />
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-slate-800">Confirm password</label>
                    <input
                        id="password_confirmation"
                        name="password_confirmation"
                        type="password"
                        class="mt-2 block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm shadow-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-200"
                        required
                    >
                    <x-form-error field="password_confirmation" />
                </div>

                <div class="flex items-center justify-between border-t border-slate-200 pt-6">
                    <a href="{{ route('login') }}" class="text-sm font-medium text-sky-700 hover:text-sky-800">Already registered?</a>
                    <button type="submit" class="inline-flex justify-center rounded-md bg-sky-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-sky-300">
                        Register
                    </button>
                </div>
            </div>
        </form>
    </div>
@endsection
