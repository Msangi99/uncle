<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>@yield('title', __('Dashboard')) - {{ config('app.name') }}</title>

        @fluxAppearance

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif

        @stack('styles')
    </head>
    <body class="font-sans antialiased dark:bg-zinc-950 dark:text-white">
        <div class="min-h-screen flex bg-gray-50 dark:bg-zinc-950">
            {{-- Sidebar --}}
            <aside class="fixed inset-y-0 left-0 z-40 w-64 flex flex-col border-r border-gray-200 dark:border-zinc-800 bg-white dark:bg-zinc-900">
                <div class="flex h-16 items-center gap-2 border-b border-gray-200 dark:border-zinc-800 px-6">
                    <span class="text-xl font-semibold text-gray-900 dark:text-white">{{ config('app.name') }}</span>
                </div>
                <nav class="flex-1 overflow-y-auto px-4 py-4 space-y-1">
                    <a
                        href="{{ route('dashboard') }}"
                        class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition
                            {{ request()->routeIs('dashboard') ? 'bg-[#FF2D20]/10 text-[#FF2D20] dark:bg-[#FF2D20]/20' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-400 dark:hover:bg-zinc-800 dark:hover:text-white' }}"
                    >
                        <span class="shrink-0 size-5 flex items-center justify-center">📊</span>
                        {{ __('Dashboard') }}
                    </a>
                    <a
                        href="{{ route('classes.index') }}"
                        class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition
                            {{ request()->routeIs('classes.*') ? 'bg-[#FF2D20]/10 text-[#FF2D20] dark:bg-[#FF2D20]/20' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-400 dark:hover:bg-zinc-800 dark:hover:text-white' }}"
                    >
                        <span class="shrink-0 size-5 flex items-center justify-center">📚</span>
                        {{ __('Classes') }}
                    </a>
                    <a
                        href="{{ route('students.index') }}"
                        class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition
                            {{ request()->routeIs('students.*') ? 'bg-[#FF2D20]/10 text-[#FF2D20] dark:bg-[#FF2D20]/20' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-400 dark:hover:bg-zinc-800 dark:hover:text-white' }}"
                    >
                        <span class="shrink-0 size-5 flex items-center justify-center">👥</span>
                        {{ __('Students') }}
                    </a>
                    <a
                        href="{{ route('payments.index') }}"
                        class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition
                            {{ request()->routeIs('payments.*') ? 'bg-[#FF2D20]/10 text-[#FF2D20] dark:bg-[#FF2D20]/20' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-400 dark:hover:bg-zinc-800 dark:hover:text-white' }}"
                    >
                        <span class="shrink-0 size-5 flex items-center justify-center">💰</span>
                        {{ __('Payments') }}
                    </a>
                    <a
                        href="{{ route('other-payments.index') }}"
                        class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition
                            {{ request()->routeIs('other-payments.*') ? 'bg-[#FF2D20]/10 text-[#FF2D20] dark:bg-[#FF2D20]/20' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-400 dark:hover:bg-zinc-800 dark:hover:text-white' }}"
                    >
                        <span class="shrink-0 size-5 flex items-center justify-center">📋</span>
                        {{ __('Malipo mengine') }}
                    </a>
                    <a
                        href="{{ route('sms.index') }}"
                        class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition
                            {{ request()->routeIs('sms.index') ? 'bg-[#FF2D20]/10 text-[#FF2D20] dark:bg-[#FF2D20]/20' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-400 dark:hover:bg-zinc-800 dark:hover:text-white' }}"
                    >
                        <span class="shrink-0 size-5 flex items-center justify-center">📱</span>
                        {{ __('SMS') }}
                    </a>
                    <a
                        href="{{ route('sms.log') }}"
                        class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition
                            {{ request()->routeIs('sms.log') ? 'bg-[#FF2D20]/10 text-[#FF2D20] dark:bg-[#FF2D20]/20' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-400 dark:hover:bg-zinc-800 dark:hover:text-white' }}"
                    >
                        <span class="shrink-0 size-5 flex items-center justify-center">📋</span>
                        {{ __('Historia ya SMS') }}
                    </a>
                    <a
                        href="{{ route('report.index') }}"
                        class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition
                            {{ request()->routeIs('report.*') ? 'bg-[#FF2D20]/10 text-[#FF2D20] dark:bg-[#FF2D20]/20' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-400 dark:hover:bg-zinc-800 dark:hover:text-white' }}"
                    >
                        <span class="shrink-0 size-5 flex items-center justify-center">📊</span>
                        {{ __('Ripoti') }}
                    </a>
                    <a
                        href="{{ route('settings.index') }}"
                        class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition
                            {{ request()->routeIs('settings.*') ? 'bg-[#FF2D20]/10 text-[#FF2D20] dark:bg-[#FF2D20]/20' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-400 dark:hover:bg-zinc-800 dark:hover:text-white' }}"
                    >
                        <span class="shrink-0 size-5 flex items-center justify-center">⚙️</span>
                        {{ __('Settings') }}
                    </a>
                </nav>
                <div class="border-t border-gray-200 dark:border-zinc-800 p-4">
                    <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-zinc-800 hover:text-gray-900 dark:hover:text-white transition {{ request()->routeIs('profile.*') ? 'bg-[#FF2D20]/10 text-[#FF2D20] dark:bg-[#FF2D20]/20' : '' }}">
                        <span class="size-8 rounded-full bg-[#FF2D20]/20 flex items-center justify-center text-[#FF2D20] font-medium shrink-0">
                            {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                        </span>
                        <span class="truncate font-medium">{{ auth()->user()->name ?? 'User' }}</span>
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="mt-2">
                        @csrf
                        <button
                            type="submit"
                            class="flex w-full items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-400 dark:hover:bg-zinc-800 dark:hover:text-white"
                        >
                            <span>🚪</span>
                            {{ __('Toka') }}
                        </button>
                    </form>
                </div>
            </aside>

            {{-- Main content --}}
            <div class="flex-1 pl-64">
                <header class="sticky top-0 z-30 flex h-16 items-center gap-4 border-b border-gray-200 dark:border-zinc-800 bg-white/80 dark:bg-zinc-900/80 backdrop-blur px-8">
                    <h1 class="text-lg font-semibold text-gray-900 dark:text-white">@yield('header', __('Dashboard'))</h1>
                </header>
                <main class="p-8">
                    @yield('content')
                </main>
            </div>
        </div>
        @livewireScripts
        @fluxScripts
        @stack('scripts')
    </body>
</html>
