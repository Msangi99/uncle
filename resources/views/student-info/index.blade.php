@extends('layouts.app')

@section('title', __('Taarifa za Mwanafunzi'))
@section('header', __('Taarifa za Mwanafunzi'))

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .select2-container--default .select2-selection--single {
            height: 42px;
            border: 1px solid rgb(63 63 70);
            border-radius: 0.75rem;
            padding: 0.4rem 0.75rem;
            background: rgb(39 39 42) !important;
            color: #fff !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered { line-height: 1.5; color: #fff !important; }
        .select2-container--default .select2-selection--single .select2-selection__placeholder { color: rgba(255,255,255,0.7) !important; }
        .select2-container--default .select2-selection--single .select2-selection__arrow { height: 40px; }
        .select2-container--default .select2-selection--single .select2-selection__arrow b { border-color: #fff transparent transparent transparent !important; }
        .select2-container--default.select2-container--open .select2-selection--single .select2-selection__arrow b { border-color: transparent transparent #fff transparent !important; }
        .select2-dropdown { border-radius: 0.75rem; border-color: rgb(63 63 70); background: rgb(39 39 42) !important; }
        .select2-container--default .select2-results__option { color: #fff !important; }
        .select2-container--default .select2-results__option[aria-selected=true] { background: rgba(255,255,255,0.1) !important; color: #fff !important; }
        .select2-container--default .select2-results__option--highlighted[aria-selected] { background: #2563eb !important; color: #fff !important; }
        .select2-container--default .select2-search--dropdown .select2-search__field {
            border: 1px solid rgb(63 63 70); background: rgb(24 24 27); color: #fff; border-radius: 0.5rem;
        }
        .select2-student-name { font-weight: 600; color: #fff !important; }
        .select2-student-ref { font-size: 0.8em; color: rgba(255,255,255,0.8) !important; margin-top: 2px; }
    </style>
@endpush

@section('content')
    <div class="space-y-6">
        <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-6 shadow-sm">
            <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-4">{{ __('Chagua mwanafunzi') }}</h2>
            <form method="GET" action="{{ route('student-info.index') }}" class="flex flex-wrap items-center gap-3">
                <div class="min-w-[220px]">
                    <label for="student_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Mwanafunzi') }}</label>
                    <select id="student_id" name="student_id" class="block w-full rounded-xl border border-zinc-200/80 dark:border-zinc-600 dark:bg-zinc-800 dark:text-white text-sm px-3 py-2.5 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                        <option value="">{{ __('Chagua mwanafunzi...') }}</option>
                        @foreach ($students as $s)
                            <option value="{{ $s->id }}" data-class="{{ e($s->classe->name) }}" data-year="{{ e($s->year) }}" {{ (request('student_id') == $s->id) ? 'selected' : '' }}>
                                {{ $s->fullname }} · {{ $s->classe->name }} · {{ $s->year }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="shrink-0 h-10 mt-2 rounded-xl bg-[#FF2D20] px-4 py-2 text-sm font-semibold text-white hover:bg-[#e0281a] leading-none">
                    {{ __('Ona taarifa') }}
                </button>
            </form>
        </div>

        @if ($student)
            {{-- Taarifa za mwanafunzi --}}
            <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">{{ __('Taarifa za mwanafunzi') }}</h2>
                <dl class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                    <div>
                        <dt class="text-sm text-gray-500 dark:text-gray-400">{{ __('Jina kamili') }}</dt>
                        <dd class="font-medium text-gray-900 dark:text-white">{{ $student->fullname }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500 dark:text-gray-400">{{ __('Darasa') }}</dt>
                        <dd class="font-medium text-gray-900 dark:text-white">{{ $student->classe->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500 dark:text-gray-400">{{ __('Mwaka') }}</dt>
                        <dd class="font-medium text-gray-900 dark:text-white">{{ $student->year }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500 dark:text-gray-400">{{ __('Aina') }}</dt>
                        <dd class="font-medium text-gray-900 dark:text-white">{{ ($student->student_type ?? 'day') === 'boarding' ? __('Boarding') : __('Day') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500 dark:text-gray-400">{{ __('Simu') }}</dt>
                        <dd class="font-medium text-gray-900 dark:text-white">{{ $student->contact ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500 dark:text-gray-400">{{ __('Barua pepe') }}</dt>
                        <dd class="font-medium text-gray-900 dark:text-white">{{ $student->email ?? '—' }}</dd>
                    </div>
                </dl>
            </div>

            {{-- Muhtasari na malipo ya ada --}}
            <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">{{ __('Malipo yote ya ada') }}</h2>

                @if ($summaryByYear !== [])
                    <div class="space-y-6">
                        @foreach ($summaryByYear as $year => $summary)
                            <div class="rounded-lg border border-zinc-100 dark:border-zinc-700 p-4">
                                <div class="flex flex-wrap items-center justify-between gap-4 mb-3">
                                    <h3 class="font-semibold text-gray-900 dark:text-white">{{ __('Mwaka') }} {{ $year }}</h3>
                                    <div class="flex flex-wrap gap-4 text-sm">
                                        <span class="text-gray-600 dark:text-gray-400">{{ __('Inayotakiwa') }}: <strong class="text-gray-900 dark:text-white">{{ number_format($summary['total_required'], 0, '.', ',') }}</strong> TZS</span>
                                        <span class="text-green-600 dark:text-green-400">{{ __('Iliyolipwa') }}: <strong>{{ number_format($summary['total_paid'], 0, '.', ',') }}</strong> TZS</span>
                                        @if ($summary['deni'] > 0)
                                            <span class="text-red-600 dark:text-red-400">{{ __('Deni') }}: <strong>{{ number_format($summary['deni'], 0, '.', ',') }}</strong> TZS</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="overflow-x-auto">
                                    <table class="min-w-full text-sm text-left">
                                        <thead>
                                            <tr class="border-b border-zinc-200 dark:border-zinc-700 text-gray-500 dark:text-gray-400">
                                                <th class="py-2 pr-4">{{ __('Tarehe') }}</th>
                                                <th class="py-2 pr-4">{{ __('Msimu') }}</th>
                                                <th class="py-2 pr-4 text-right">{{ __('Kiasi') }} (TZS)</th>
                                            </tr>
                                        </thead>
                                        <tbody class="text-gray-900 dark:text-white">
                                            @foreach ($summary['payments'] as $p)
                                                <tr class="border-b border-zinc-100 dark:border-zinc-800">
                                                    <td class="py-2 pr-4">{{ $p->paid_at ? $p->paid_at->format('d/m/Y H:i') : '—' }}</td>
                                                    <td class="py-2 pr-4">{{ __('Msimu') }} {{ $p->term_number }}</td>
                                                    <td class="py-2 pr-4 text-right font-medium">{{ number_format($p->amount, 0, '.', ',') }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 dark:text-gray-400">{{ __('Hakuna malipo ya ada bado kwa mwanafunzi huyu.') }}</p>
                @endif
            </div>
        @else
            @if (request('student_id'))
                <p class="text-gray-500 dark:text-gray-400">{{ __('Mwanafunzi hajapatikana.') }}</p>
            @else
                <p class="text-gray-500 dark:text-gray-400">{{ __('Chagua mwanafunzi hapa juu ili kuona taarifa zake na malipo ya ada.') }}</p>
            @endif
        @endif
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
                        var name = item.text.split(' · ')[0] || item.text;
                        return jQuery('<span><span class="select2-student-name">' + name + '</span><br><span class="select2-student-ref">Darasa: ' + (c || '') + ' &nbsp; Mwaka: ' + (y || '') + '</span></span>');
                    },
                    templateSelection: function (item) {
                        if (!item.id) return item.text;
                        var parts = item.text.split(' · ');
                        return (parts[0] || '') + ' — ' + (parts[1] || '') + ', ' + (parts[2] || '');
                    }
                });
            }
        });
    </script>
@endpush
