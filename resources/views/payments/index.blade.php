@extends('layouts.app')

@section('title', __('Payments'))
@section('header', __('Payments'))

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        /* Select2: nyeusi (background) na maandishi meupe */
        .select2-container--default .select2-selection--single {
            height: 42px;
            border: 1px solid rgb(63 63 70);
            border-radius: 0.75rem;
            padding: 0.4rem 0.75rem;
            background: rgb(39 39 42) !important;
            color: #fff !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 1.5;
            color: #fff !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__placeholder { color: rgba(255,255,255,0.7) !important; }
        .select2-container--default .select2-selection--single .select2-selection__arrow { height: 40px; }
        .select2-container--default .select2-selection--single .select2-selection__arrow b { border-color: #fff transparent transparent transparent !important; }
        .select2-container--default.select2-container--open .select2-selection--single .select2-selection__arrow b { border-color: transparent transparent #fff transparent !important; }
        /* Dropdown: nyeusi na maandishi meupe */
        .select2-dropdown {
            border-radius: 0.75rem;
            border-color: rgb(63 63 70);
            background: rgb(39 39 42) !important;
        }
        .select2-container--default .select2-results__option {
            color: #fff !important;
        }
        .select2-container--default .select2-results__option[aria-selected=true] { background: rgba(255,255,255,0.1) !important; color: #fff !important; }
        .select2-container--default .select2-results__option--highlighted[aria-selected] { background: #2563eb !important; color: #fff !important; }
        .select2-container--default .select2-search--dropdown .select2-search__field {
            border: 1px solid rgb(63 63 70);
            background: rgb(24 24 27);
            color: #fff;
            border-radius: 0.5rem;
        }
        .select2-student-name { font-weight: 600; color: #fff !important; }
        .select2-student-ref { font-size: 0.8em; color: rgba(255,255,255,0.8) !important; margin-top: 2px; }
    </style>
@endpush

@section('content')
    <div class="flex justify-center">
        <div class="w-full max-w-md space-y-6">
            @if (session('success'))
                <div class="rounded-lg bg-green-50 dark:bg-green-900/20 px-4 py-3 text-sm text-green-700 dark:text-green-400">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="rounded-lg bg-red-50 dark:bg-red-900/20 px-4 py-3 text-sm text-red-700 dark:text-red-400">
                    {{ $errors->first() }}
                </div>
            @endif

            {{-- Form kati (centered) --}}
            <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">{{ __('Ingiza Malipo') }}</h2>
                <form method="POST" action="{{ route('payments.store') }}" class="space-y-5">
                    @csrf

                    <div>
                        <label for="student_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            {{ __('Mwanafunzi') }}
                        </label>
                        <select
                            id="student_id"
                            name="student_id"
                            required
                            class="block w-full rounded-xl border border-zinc-200/80 dark:border-zinc-600 dark:bg-zinc-800 dark:text-white text-sm"
                        >
                            <option value="">{{ __('Chagua mwanafunzi...') }}</option>
                            @foreach ($students as $s)
                                <option value="{{ $s->id }}" data-class="{{ e($s->classe->name) }}" data-year="{{ e($s->year) }}" {{ old('student_id') == $s->id ? 'selected' : '' }}>
                                    {{ $s->fullname }} · {{ $s->classe->name }} · {{ $s->year }}
                                </option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ __('Jina · Darasa · Mwaka') }}</p>
                    </div>

                    <div>
                        <label for="year" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Mwaka') }}</label>
                        <flux:input
                            type="text"
                            name="year"
                            id="year"
                            value="{{ old('year', request('year', date('Y'))) }}"
                            required
                            maxlength="50"
                            placeholder="{{ date('Y') }}"
                            variant="outline"
                            class="rounded-xl border-zinc-200/80 dark:border-white/10"
                        />
                    </div>

                    <div>
                        <label for="amount" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Kiasi (TZS)') }}</label>
                        <flux:input
                            type="text"
                            name="amount"
                            id="amount"
                            value="{{ old('amount') }}"
                            required
                            inputmode="decimal"
                            placeholder="0.00"
                            variant="outline"
                            class="rounded-xl border-zinc-200/80 dark:border-white/10"
                        />
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                            {{ __('Mfumo utabainisha msimu kiotomatiki kulingana na kiasi kilicholipwa.') }}
                        </p>
                    </div>

                    <flux:button
                        type="submit"
                        variant="filled"
                        color="blue"
                        class="w-full justify-center rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5"
                    >
                        {{ __('Hifadhi Malipo') }}
                    </flux:button>
                </form>
            </div>

            @if ($recentPayments->isNotEmpty())
                <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 overflow-hidden shadow-sm">
                    <h3 class="px-4 py-3 text-sm font-semibold text-gray-900 dark:text-white border-b border-zinc-200 dark:border-zinc-700">
                        {{ __('Malipo ya hivi karibuni') }}
                    </h3>
                    <div class="divide-y divide-zinc-200 dark:divide-zinc-700 max-h-64 overflow-y-auto">
                        @foreach ($recentPayments as $p)
                            <div class="px-4 py-2.5 text-sm">
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-700 dark:text-gray-300">{{ $p->student->fullname }} ({{ $p->student->classe->name }})</span>
                                    <span class="font-medium text-gray-900 dark:text-white">{{ number_format($p->amount, 2, '.', ',') }} TZS</span>
                                </div>
                                @php $termLabel = config('school.terms.'.$p->term_number.'.label', __('Msimu').' '.$p->term_number); @endphp
                                <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $termLabel }} / {{ $p->year }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof jQuery !== 'undefined' && jQuery.fn.select2) {
                jQuery('#student_id').select2({
                    placeholder: '{{ __('Chagua mwanafunzi...') }}',
                    allowClear: true,
                    width: '100%',
                    templateResult: function (item) {
                        if (!item.id) return item.text;
                        var c = jQuery(item.element).data('class');
                        var y = jQuery(item.element).data('year');
                        var $r = jQuery('<span><span class="select2-student-name">' + item.text.split(' · ')[0] + '</span><br><span class="select2-student-ref">Darasa: ' + (c || '') + ' &nbsp; Mwaka: ' + (y || '') + '</span></span>');
                        return $r;
                    },
                    templateSelection: function (item) {
                        if (!item.id) return item.text;
                        var parts = item.text.split(' · ');
                        return parts[0] + ' — ' + (parts[1] || '') + ', ' + (parts[2] || '');
                    }
                });
            }
        });
    </script>
@endpush
