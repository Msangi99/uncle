@extends('layouts.app')

@section('title', __('Mipangilio'))
@section('header', __('Mipangilio'))

@section('content')
    <div class="space-y-8 max-w-3xl">
        {{-- Orodha ya misimu na miezi (kutoka config) --}}
        <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-6 shadow-sm">
            <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-2">{{ __('Orodha ya misimu na miezi') }}</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">{{ __('Mfumo unatumia orodha hii kila mahali (wanafunzi, malipo, mipangilio).') }}</p>
            <div class="grid gap-4 sm:grid-cols-2">
                @foreach (config('school.terms', []) as $t => $termCfg)
                    @php
                        $monthNames = collect($termCfg['months'] ?? [])->map(fn($m) => config('school.months.'.$m, (string)$m))->join(', ');
                    @endphp
                    <div class="flex items-baseline gap-2 text-sm">
                        <span class="font-medium text-gray-700 dark:text-gray-300">{{ $termCfg['short'] ?? "M$t" }}:</span>
                        <span class="text-gray-600 dark:text-gray-400">{{ $termCfg['label'] ?? "Msimu $t" }}</span>
                        @if ($monthNames)
                            <span class="text-gray-500 dark:text-gray-500">({{ $monthNames }})</span>
                        @endif
                    </div>
                @endforeach
            </div>
            <details class="mt-4">
                <summary class="text-sm cursor-pointer text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300">{{ __('Orodha ya miezi 1–12') }}</summary>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                    @foreach (config('school.months', []) as $num => $name)
                        {{ $num }} = {{ $name }}@if (!$loop->last), @endif
                    @endforeach
                </p>
            </details>
        </div>

        @if (session('success'))
            <div class="rounded-lg bg-green-50 dark:bg-green-900/20 px-4 py-3 text-sm text-green-700 dark:text-green-400">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('settings.store') }}" class="space-y-8">
            @csrf

            {{-- Fieldset 1: Ada kulingana na madarasa --}}
            <fieldset class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-6 shadow-sm">
                <legend class="text-base font-semibold text-gray-900 dark:text-white px-2">
                    {{ __('Ada kulingana na madarasa') }}
                </legend>
                <p class="mt-1 mb-4 text-sm text-gray-500 dark:text-gray-400">
                    {{ __('Weka kiasi cha ada (TZS au kitengo chako) kwa kila darasa.') }}
                </p>
                <div class="space-y-4">
                    @forelse ($classes as $class)
                        @php
                            $fee = $classFees->get($class->id);
                            $adaValue = old('ada.'.$class->id);
                            if ($adaValue !== null) {
                                $adaDisplay = number_format((float) str_replace(',', '', $adaValue), 2, '.', ',');
                            } else {
                                $adaDisplay = $fee ? number_format($fee->amount, 2, '.', ',') : '';
                            }
                        @endphp
                        <div class="flex flex-wrap items-center gap-3">
                            <label for="ada-{{ $class->id }}" class="w-40 shrink-0 text-sm font-medium text-gray-700 dark:text-gray-300">
                                {{ $class->name }}
                            </label>
                            <div class="flex flex-1 min-w-[140px] items-center gap-2">
                                <flux:input
                                    type="text"
                                    name="ada[{{ $class->id }}]"
                                    id="ada-{{ $class->id }}"
                                    value="{{ $adaDisplay }}"
                                    placeholder="0.00"
                                    inputmode="decimal"
                                    variant="outline"
                                    class="rounded-xl border-zinc-200/80 dark:border-white/10"
                                />
                                <span class="shrink-0 text-sm text-gray-500 dark:text-gray-400">TZS</span>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Hakuna madarasa bado. Ongeza madarasa kwanza.') }}</p>
                    @endforelse
                </div>
            </fieldset>

            {{-- Fieldset 2: Asilimia ya ada iliyolipwa kwa kila msimu (misimu 4) --}}
            <fieldset class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-6 shadow-sm">
                <legend class="text-base font-semibold text-gray-900 dark:text-white px-2">
                    {{ __('Zitalipwa kwa misimu minne kwa mwaka') }}
                </legend>
                <p class="mt-1 mb-4 text-sm text-gray-500 dark:text-gray-400">
                    {{ __('Weka asilimia ya ada inayolipwa kwa kila msimu. Mf. Msimu 1 = 25% inamaanisha msimu wa kwanza ulipapo ada ni 25% ya jumla.') }}
                </p>
                <div class="grid gap-4 sm:grid-cols-2">
                    @foreach (config('school.terms', []) as $term => $termCfg)
                        @php $tp = $termPercentages->get($term); @endphp
                        <div class="flex flex-wrap items-center gap-3">
                            <label for="term-{{ $term }}" class="w-40 shrink-0 text-sm font-medium text-gray-700 dark:text-gray-300">
                                {{ $termCfg['label'] ?? __('Msimu') . ' ' . $term }}
                            </label>
                            <flux:input
                                type="number"
                                name="term_percent[{{ $term }}]"
                                id="term-{{ $term }}"
                                value="{{ old('term_percent.'.$term, $tp ? $tp->percent_paid : 25) }}"
                                min="0"
                                max="100"
                                step="0.01"
                                required
                                variant="outline"
                                class="w-24 rounded-xl border-zinc-200/80 dark:border-white/10"
                            />
                            <span class="text-sm text-gray-500 dark:text-gray-400">%</span>
                        </div>
                    @endforeach
                    @if (empty(config('school.terms')))
                        @for ($term = 1; $term <= 4; $term++)
                            @php $tp = $termPercentages->get($term); @endphp
                            <div class="flex flex-wrap items-center gap-3">
                                <label for="term-{{ $term }}" class="w-24 shrink-0 text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Msimu') }} {{ $term }}</label>
                                <flux:input type="number" name="term_percent[{{ $term }}]" id="term-{{ $term }}" value="{{ old('term_percent.'.$term, $tp ? $tp->percent_paid : 25) }}" min="0" max="100" step="0.01" required variant="outline" class="w-24 rounded-xl border-zinc-200/80 dark:border-white/10" />
                                <span class="text-sm text-gray-500 dark:text-gray-400">%</span>
                            </div>
                        @endfor
                    @endif
                </div>
            </fieldset>

            <div class="flex justify-end">
                <flux:button
                    type="submit"
                    variant="filled"
                    color="blue"
                    class="rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5"
                >
                    {{ __('Hifadhi Mipangilio') }}
                </flux:button>
            </div>
        </form>
    </div>
@endsection
