@extends('layouts.app')

@section('title', __('Dashboard'))
@section('header', __('Dashboard'))

@section('content')
    <div class="space-y-6 pb-8">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Karibu, ') }}{{ auth()->user()->name ?? __('User') }}</h2>
                <p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">{{ __('Muhtasari wa mfumo wa usimamizi wa shule.') }}</p>
            </div>
            <form method="GET" action="{{ route('dashboard') }}" class="flex items-center gap-2">
                <label class="text-sm text-gray-600 dark:text-gray-400">{{ __('Mwaka') }}:</label>
                <select name="year" onchange="this.form.submit()" class="rounded-lg border border-zinc-300 dark:border-zinc-600 dark:bg-zinc-800 dark:text-white px-2.5 py-1.5 text-sm">
                    @php
                        $currentYear = (int) date('Y');
                        $selectedYear = (int) ($year ?? $currentYear);
                    @endphp
                    @for ($y = $currentYear; $y >= $currentYear - 10; $y--)
                        <option value="{{ $y }}" {{ $selectedYear === $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </form>
        </div>

        {{-- Stats cards --}}
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <a href="{{ route('classes.index') }}" class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-5 shadow-sm hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition">
                <div class="flex items-center gap-3">
                    <span class="flex size-12 items-center justify-center rounded-lg bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 text-xl">📚</span>
                    <div>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $classesCount ?? 0 }}</p>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Madarasa') }}</p>
                    </div>
                </div>
            </a>
            <a href="{{ route('students.index') }}" class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-5 shadow-sm hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition">
                <div class="flex items-center gap-3">
                    <span class="flex size-12 items-center justify-center rounded-lg bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400 text-xl">👥</span>
                    <div>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $studentsCount ?? 0 }}</p>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Wanafunzi') }}</p>
                    </div>
                </div>
            </a>
            <a href="{{ route('payments.index') }}?year={{ $year ?? date('Y') }}" class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-5 shadow-sm hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition">
                <div class="flex items-center gap-3">
                    <span class="flex size-12 items-center justify-center rounded-lg bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 text-xl">💰</span>
                    <div>
                        <p class="text-xl font-bold text-gray-900 dark:text-white">{{ number_format($paymentsThisYear ?? 0, 0, '.', ',') }}</p>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Jumla ya malipo') }} ({{ $year ?? date('Y') }})</p>
                    </div>
                </div>
            </a>
            <a href="{{ route('sms.log') }}" class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-5 shadow-sm hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition">
                <div class="flex items-center gap-3">
                    <span class="flex size-12 items-center justify-center rounded-lg bg-violet-100 dark:bg-violet-900/30 text-violet-600 dark:text-violet-400 text-xl">📱</span>
                    <div>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $smsSentCount ?? 0 }}</p>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('SMS zilizotumwa') }}</p>
                    </div>
                </div>
            </a>
        </div>

        {{-- Quick actions --}}
        <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-5 shadow-sm">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">{{ __('Vitendo vya haraka') }}</h3>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('students.index') }}?add=1" class="inline-flex items-center gap-1.5 rounded-lg bg-[#FF2D20] px-3 py-2 text-sm font-medium text-white hover:bg-[#e0281a]">{{ __('Ongeza Mwanafunzi') }}</a>
                <a href="{{ route('payments.index') }}" class="inline-flex items-center gap-1.5 rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-zinc-50 dark:hover:bg-zinc-700">{{ __('Ingiza Malipo') }}</a>
                <a href="{{ route('sms.index') }}" class="inline-flex items-center gap-1.5 rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-zinc-50 dark:hover:bg-zinc-700">{{ __('Tuma SMS') }}</a>
                <a href="{{ route('report.index') }}" class="inline-flex items-center gap-1.5 rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-zinc-50 dark:hover:bg-zinc-700">{{ __('Ripoti') }}</a>
                <a href="{{ route('settings.index') }}" class="inline-flex items-center gap-1.5 rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-zinc-50 dark:hover:bg-zinc-700">{{ __('Mipangilio') }}</a>
            </div>
        </div>

        {{-- Recent payments --}}
        <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 overflow-hidden shadow-sm">
            <div class="flex items-center justify-between px-5 py-3 border-b border-zinc-200 dark:border-zinc-700">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white">{{ __('Malipo ya hivi karibuni') }}</h3>
                <a href="{{ route('payments.index') }}" class="text-sm font-medium text-[#FF2D20] hover:underline">{{ __('Ona zote') }}</a>
            </div>
            @if (isset($recentPayments) && $recentPayments->isNotEmpty())
                <div class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @foreach ($recentPayments as $p)
                        <div class="flex items-center justify-between px-5 py-3 text-sm">
                            <div>
                                <span class="font-medium text-gray-900 dark:text-white">{{ $p->student->fullname ?? '—' }}</span>
                                <span class="text-gray-500 dark:text-gray-400">({{ $p->student->classe->name ?? '—' }})</span>
                            </div>
                            <div class="text-right">
                                <span class="font-medium text-gray-900 dark:text-white">{{ number_format($p->amount, 0, '.', ',') }} TZS</span>
                                <span class="text-gray-500 dark:text-gray-400 text-xs block">{{ $p->paid_at?->format('d/m/Y H:i') ?? $p->year }} · M{{ $p->term_number }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="px-5 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                    {{ __('Hakuna malipo ya hivi karibuni.') }} <a href="{{ route('payments.index') }}" class="text-[#FF2D20] hover:underline">{{ __('Ingiza malipo') }}</a>
                </div>
            @endif
        </div>
    </div>
@endsection
