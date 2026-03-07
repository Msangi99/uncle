@extends('layouts.app')

@section('title', __('Ripoti'))
@section('header', __('Ripoti'))

@section('content')
    <div class="space-y-6 pb-8 max-w-xl">
        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Chagua darasa na mwaka kisha pakua Excel ya wanafunzi: jina, status ya malipo kwa muhula (M1–M4), ada inayotakiwa, alilolipa, na deni.') }}</p>

        <form method="GET" action="{{ route('report.export') }}" target="_blank" class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-6 shadow-sm space-y-5">
            <div>
                <label for="report-class_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Darasa') }}</label>
                <select name="class_id" id="report-class_id" required class="block w-full rounded-xl border border-zinc-200/80 dark:border-white/10 dark:bg-zinc-800 dark:text-white shadow-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 text-sm px-3 py-2.5">
                    <option value="">{{ __('Chagua darasa') }}</option>
                    @foreach ($classes as $c)
                        <option value="{{ $c->id }}" {{ (isset($classId) && $classId == $c->id) ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="report-year" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Mwaka') }}</label>
                <input type="text" name="year" id="report-year" value="{{ $year ?? date('Y') }}" required maxlength="50" placeholder="{{ date('Y') }}" class="block w-full rounded-xl border border-zinc-200/80 dark:border-white/10 dark:bg-zinc-800 dark:text-white shadow-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 text-sm px-3 py-2.5" />
            </div>
            <div class="pt-2">
                <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-[#FF2D20] px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-[#e0281a]">
                    <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    {{ __('Tengeneza / Pakua Excel') }}
                </button>
            </div>
        </form>
    </div>
@endsection
