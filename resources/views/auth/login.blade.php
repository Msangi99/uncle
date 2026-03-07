<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ __('Ingia') }} - {{ config('app.name') }}</title>

        @fluxAppearance

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
    </head>
    <body class="font-sans antialiased dark:bg-zinc-950 dark:text-white">
        <div class="min-h-screen flex flex-col items-center justify-center bg-gray-50 dark:bg-zinc-950 px-4 selection:bg-blue-600 selection:text-white">
            <div class="w-full max-w-md">
                <div class="text-center mb-8">
                    <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">{{ config('app.name') }}</h1>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('Ingia kwa akaunti yako') }}</p>
                </div>

                <div class="rounded-xl bg-white dark:bg-zinc-900 shadow-lg ring-1 ring-gray-200 dark:ring-zinc-800 p-6">
                    @if ($errors->any())
                        <div class="mb-4 rounded-lg bg-red-50 dark:bg-red-900/20 px-4 py-3 text-sm text-red-700 dark:text-red-400">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}" class="space-y-5">
                        @csrf

                        <flux:input
                            type="email"
                            name="email"
                            label="{{ __('Barua pepe') }}"
                            placeholder="admin@example.com"
                            value="{{ old('email') }}"
                            required
                            autofocus
                            autocomplete="email"
                            icon-leading="envelope"
                            variant="outline"
                            class="rounded-xl border-zinc-200/80 dark:border-white/10 focus-within:ring-2 focus-within:ring-blue-500/20 focus-within:border-blue-500 dark:focus-within:border-blue-400"
                        />

                        <flux:input
                            type="password"
                            name="password"
                            label="{{ __('Nenosiri') }}"
                            required
                            autocomplete="current-password"
                            icon-leading="lock-closed"
                            variant="outline"
                            viewable
                            class="rounded-xl border-zinc-200/80 dark:border-white/10 focus-within:ring-2 focus-within:ring-blue-500/20 focus-within:border-blue-500 dark:focus-within:border-blue-400"
                        />

                        <flux:checkbox
                            name="remember"
                            class="rounded border-zinc-300 dark:border-zinc-600 data-[checked]:bg-blue-600 data-[checked]:border-blue-600 focus:ring-blue-500"
                        >
                            {{ __('Nikumbuke') }}
                        </flux:checkbox>

                        <flux:button
                            type="submit"
                            variant="filled"
                            color="blue"
                            class="w-full justify-center rounded-xl bg-blue-600 hover:bg-blue-700 dark:bg-blue-600 dark:hover:bg-blue-500 text-white font-semibold shadow-sm focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-zinc-900 py-2.5"
                        >
                            {{ __('Ingia') }}
                        </flux:button>
                    </form>
                </div>

                <p class="mt-6 text-center text-xs text-gray-500 dark:text-gray-400">
                    {{ __('Admin: admin@example.com / password') }}
                </p>
            </div>
        </div>
        @livewireScripts
        @fluxScripts
    </body>
</html>
