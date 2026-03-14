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

        {{-- Siri za SMS (SMS.co.tz) --}}
        <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-6 shadow-sm">
            <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-2">{{ __('Siri za SMS (SMS.co.tz)') }}</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">{{ __('Weka API key na Sender ID kutoka sms.co.tz ili kutumia ukurasa wa SMS. Unaweza pia kuacha tupu na kutumia thamani kutoka .env.') }}</p>
            <form method="POST" action="{{ route('settings.sms.store') }}" class="space-y-4">
                @csrf
                <div class="grid gap-4 sm:grid-cols-1">
                    <div>
                        <label for="sms_api_key" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('API Key') }}</label>
                        <flux:input
                            type="text"
                            name="sms_api_key"
                            id="sms_api_key"
                            value="{{ old('sms_api_key', $smsCredential?->api_key ?? '') }}"
                            placeholder="{{ __('Weka API key') }}"
                            variant="outline"
                            class="w-full rounded-xl border-zinc-200/80 dark:border-white/10"
                        />
                    </div>
                    <div>
                        <label for="sms_sender_id" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Sender ID') }}</label>
                        <flux:input
                            type="text"
                            name="sms_sender_id"
                            id="sms_sender_id"
                            value="{{ old('sms_sender_id', $smsCredential?->sender_id ?? '') }}"
                            placeholder="{{ __('Weka Sender ID') }}"
                            variant="outline"
                            class="w-full rounded-xl border-zinc-200/80 dark:border-white/10"
                        />
                    </div>
                    <div>
                        <label for="sms_url" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('URL ya API (si lazima)') }}</label>
                        <flux:input
                            type="url"
                            name="sms_url"
                            id="sms_url"
                            value="{{ old('sms_url', $smsCredential?->url ?? 'https://www.sms.co.tz/api.php') }}"
                            placeholder="https://www.sms.co.tz/api.php"
                            variant="outline"
                            class="w-full rounded-xl border-zinc-200/80 dark:border-white/10"
                        />
                    </div>
                </div>
                <div class="flex justify-end">
                    <flux:button type="submit" variant="filled" color="blue" class="rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5">
                        {{ __('Hifadhi Siri za SMS') }}
                    </flux:button>
                </div>
            </form>
        </div>

        <form method="POST" action="{{ route('settings.store') }}" class="space-y-8">
            @csrf
            {{-- Asilimia ya ada iliyolipwa kwa kila msimu (misimu 4) --}}
            <fieldset class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-6 shadow-sm">
                <legend class="text-base font-semibold text-gray-900 dark:text-white px-2">
                    {{ __('Zitalipwa kwa misimu minne kwa mwaka') }}
                </legend>
                <p class="mt-1 mb-4 text-sm text-gray-500 dark:text-gray-400">
                    {{ __('Weka asilimia ya ada inayolipwa kwa kila msimu. Asilimia hii inatumika kwa ada inayomfaa mwanafunzi (Day au Boarding). Mf. Msimu 1 = 25% inamaanisha mwanafunzi anahitaji kulipa 25% ya ada yake (ya Day au Boarding).') }}
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

            {{-- Fieldset 3: Malipo mengi (tahadhari, maktaba, ream, etc.) --}}
            <fieldset class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-6 shadow-sm">
                <legend class="text-base font-semibold text-gray-900 dark:text-white px-2">
                    {{ __('Malipo mengi') }}
                </legend>
                <p class="mt-1 mb-4 text-sm text-gray-500 dark:text-gray-400">
                    {{ __('Weka kiasi (TZS) kwa kila aina ya malipo. Pesa ya mtihani wa taifa inatumika kwa madarasa ya mtihani tu.') }}
                </p>
                @php
                    $ptSettings = $paymentTypeSettings ?? \App\Models\PaymentTypeSetting::getInstance();
                    $typeKeys = \App\Models\PaymentTypeSetting::typeKeys();
                @endphp
                <div class="space-y-4">
                    @foreach ($typeKeys as $key => $label)
                        @php
                            $val = old('payment_types.'.$key);
                            if ($val === null) {
                                $val = $ptSettings->{$key} ?? 0;
                            }
                            $val = is_numeric($val) ? number_format((float)$val, 2, '.', ',') : (is_string($val) ? $val : '');
                        @endphp
                        <div class="flex flex-wrap items-center gap-3">
                            <label for="pt-{{ $key }}" class="w-56 shrink-0 text-sm font-medium text-gray-700 dark:text-gray-300">
                                {{ $label }}
                            </label>
                            <div class="flex flex-1 min-w-[140px] items-center gap-2">
                                <flux:input
                                    type="text"
                                    name="payment_types[{{ $key }}]"
                                    id="pt-{{ $key }}"
                                    value="{{ $val }}"
                                    placeholder="0.00"
                                    inputmode="decimal"
                                    variant="outline"
                                    class="rounded-xl border-zinc-200/80 dark:border-white/10"
                                />
                                <span class="shrink-0 text-sm text-gray-500 dark:text-gray-400">TZS</span>
                            </div>
                        </div>
                    @endforeach
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
