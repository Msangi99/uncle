@extends('layouts.app')

@section('title', __('Hariri Wasifu'))
@section('header', __('Hariri Wasifu'))

@section('content')
    <div class="max-w-xl space-y-6">
        @if (session('success'))
            <div class="rounded-lg bg-green-50 dark:bg-green-900/20 px-4 py-3 text-sm text-green-700 dark:text-green-400">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="rounded-lg bg-red-50 dark:bg-red-900/20 px-4 py-3 text-sm text-red-700 dark:text-red-400">
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-6 shadow-sm">
            <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-4">{{ __('Taarifa za wasifu') }}</h2>

            <form method="POST" action="{{ route('profile.update') }}" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label for="name" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Jina') }}</label>
                    <flux:input
                        type="text"
                        name="name"
                        id="name"
                        value="{{ old('name', $user->name) }}"
                        required
                        variant="outline"
                        class="w-full rounded-xl border-zinc-200/80 dark:border-white/10"
                    />
                </div>

                <div>
                    <label for="email" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Barua pepe') }}</label>
                    <flux:input
                        type="email"
                        name="email"
                        id="email"
                        value="{{ old('email', $user->email) }}"
                        required
                        variant="outline"
                        class="w-full rounded-xl border-zinc-200/80 dark:border-white/10"
                    />
                </div>

                <div class="border-t border-zinc-200 dark:border-zinc-700 pt-4 mt-6">
                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Badilisha nenosiri (si lazima)') }}</p>
                    <div class="space-y-4">
                        <div>
                            <label for="current_password" class="mb-1 block text-sm text-gray-600 dark:text-gray-400">{{ __('Nenosiri la sasa') }}</label>
                            <flux:input
                                type="password"
                                name="current_password"
                                id="current_password"
                                variant="outline"
                                class="w-full rounded-xl border-zinc-200/80 dark:border-white/10"
                            />
                        </div>
                        <div>
                            <label for="password" class="mb-1 block text-sm text-gray-600 dark:text-gray-400">{{ __('Nenosiri jipya') }}</label>
                            <flux:input
                                type="password"
                                name="password"
                                id="password"
                                variant="outline"
                                class="w-full rounded-xl border-zinc-200/80 dark:border-white/10"
                            />
                        </div>
                        <div>
                            <label for="password_confirmation" class="mb-1 block text-sm text-gray-600 dark:text-gray-400">{{ __('Thibitisha nenosiri jipya') }}</label>
                            <flux:input
                                type="password"
                                name="password_confirmation"
                                id="password_confirmation"
                                variant="outline"
                                class="w-full rounded-xl border-zinc-200/80 dark:border-white/10"
                            />
                        </div>
                    </div>
                </div>

                <div class="flex justify-end pt-2">
                    <flux:button
                        type="submit"
                        variant="filled"
                        color="blue"
                        class="rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5"
                    >
                        {{ __('Hifadhi Wasifu') }}
                    </flux:button>
                </div>
            </form>
        </div>
    </div>
@endsection
