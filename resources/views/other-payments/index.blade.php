@extends('layouts.app')

@section('title', __('Malipo mengine'))
@section('header', __('Malipo mengine'))

@push('styles')
    <style>
        .row-debt { background-color: #fef2f2 !important; }
        .dark .row-debt { background-color: rgba(127, 29, 29, 0.3) !important; }
        .row-clear { background-color: #f0fdf4 !important; }
        .dark .row-clear { background-color: rgba(20, 83, 45, 0.2) !important; }
        #other-payments-table-body tr { cursor: pointer; }
    </style>
@endpush

@section('content')
    <div class="space-y-6 pb-8">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Orodha ya wanafunzi na hali ya malipo mengine. Mstari mwekundu = deni, kijani = sawa.') }}</p>
            <form method="GET" action="{{ route('other-payments.index') }}" class="flex items-center gap-2 flex-wrap">
                <label class="text-sm text-gray-600 dark:text-gray-400">{{ __('Mwaka') }}:</label>
                <input type="text" name="year" value="{{ $year }}" placeholder="{{ date('Y') }}" class="rounded-lg border border-zinc-300 dark:border-zinc-600 dark:bg-zinc-800 dark:text-white px-2.5 py-1.5 text-sm w-20" />
                <label class="text-sm text-gray-600 dark:text-gray-400">{{ __('Msimu') }}:</label>
                <select name="term" class="rounded-lg border border-zinc-300 dark:border-zinc-600 dark:bg-zinc-800 dark:text-white px-2.5 py-1.5 text-sm">
                    @foreach (config('school.terms', []) as $t => $cfg)
                        <option value="{{ $t }}" {{ $term == $t ? 'selected' : '' }}>{{ $cfg['label'] ?? "Msimu $t" }}</option>
                    @endforeach
                    @if (empty(config('school.terms')))
                        @for ($t = 1; $t <= 4; $t++)<option value="{{ $t }}" {{ $term == $t ? 'selected' : '' }}>Msimu {{ $t }}</option>@endfor
                    @endif
                </select>
                <button type="submit" class="rounded-lg bg-[#FF2D20] px-3 py-1.5 text-sm text-white">{{ __('Ona') }}</button>
            </form>
        </div>

        @if (session('success'))
            <div class="rounded-lg bg-green-50 dark:bg-green-900/20 px-4 py-3 text-sm text-green-700 dark:text-green-400">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="rounded-lg bg-red-50 dark:bg-red-900/20 px-4 py-3 text-sm text-red-700 dark:text-red-400">
                {{ session('error') }}
            </div>
        @endif

        @if ($students->isEmpty())
            <div class="rounded-xl border-2 border-dashed border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-12 text-center">
                <p class="text-gray-500 dark:text-gray-400">{{ __('Hakuna wanafunzi.') }}</p>
            </div>
        @else
            <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 overflow-hidden shadow-sm">
                <div class="px-4 py-3 border-b border-zinc-200 dark:border-zinc-700">
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white">{{ __('Wanafunzi') }}</h3>
                </div>
                <div class="flex flex-wrap items-center justify-between gap-3 px-5 py-4 border-b border-zinc-200 dark:border-zinc-700 bg-zinc-50/80 dark:bg-zinc-800/50">
                    <span class="text-sm text-zinc-600 dark:text-zinc-400">
                        {{ __('Ona') }} <strong id="op-showing-count">{{ $students->count() }}</strong> {{ __('kati ya') }} <strong id="op-total-count">{{ $students->count() }}</strong> {{ __('wanafunzi') }}
                    </span>
                    <label class="flex items-center gap-2 text-sm text-zinc-600 dark:text-zinc-400">
                        <span>{{ __('Tafuta') }}:</span>
                        <input type="search" id="op-search" placeholder="{{ __('Andika kutafutia...') }}" class="rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 dark:text-white px-3 py-2 text-sm w-48 sm:w-56" autocomplete="off" />
                    </label>
                </div>
                <div class="p-4 overflow-x-auto">
                <flux:table class="dark:text-zinc-200">
                    <flux:table.columns>
                        <flux:table.column align="center" class="w-14">#</flux:table.column>
                        <flux:table.column>{{ __('Jina kamili') }}</flux:table.column>
                        <flux:table.column>{{ __('Darasa') }}</flux:table.column>
                        <flux:table.column>{{ __('Mwaka') }}</flux:table.column>
                        <flux:table.column>{{ __('Hali') }}</flux:table.column>
                        <flux:table.column>{{ __('Lililolipwa') }}</flux:table.column>
                        <flux:table.column>{{ __('Kinachohitajika') }}</flux:table.column>
                    </flux:table.columns>
                    <flux:table.rows id="other-payments-table-body">
                        @foreach ($students as $s)
                            <flux:table.row
                                class="{{ ($s->other_payment_below ?? false) ? 'row-debt' : 'row-clear' }}"
                                data-student-id="{{ $s->id }}"
                                data-fullname="{{ e($s->fullname) }}"
                                data-class-name="{{ e($s->classe->name ?? '') }}"
                                data-year="{{ e($s->year) }}"
                                data-contact="{{ e($s->contact ?? '') }}"
                                data-email="{{ e($s->email ?? '') }}"
                                data-term="{{ $term }}"
                                data-year-val="{{ $year }}"
                            >
                                <flux:table.cell align="center">{{ $loop->iteration }}</flux:table.cell>
                                <flux:table.cell variant="strong">{{ $s->fullname }}</flux:table.cell>
                                <flux:table.cell>{{ $s->classe->name ?? '—' }}</flux:table.cell>
                                <flux:table.cell>{{ $s->year }}</flux:table.cell>
                                <flux:table.cell>
                                    @if ($s->other_payment_below ?? false)
                                        <span class="text-red-700 dark:text-red-400 font-medium">{{ __('Deni') }}</span>
                                    @else
                                        <span class="text-green-700 dark:text-green-400">{{ __('Sawa') }}</span>
                                    @endif
                                </flux:table.cell>
                                <flux:table.cell>{{ number_format($s->other_payment_paid ?? 0, 0, '.', ',') }} TZS</flux:table.cell>
                                <flux:table.cell>{{ number_format($s->other_payment_required ?? 0, 0, '.', ',') }} TZS</flux:table.cell>
                            </flux:table.row>
                        @endforeach
                    </flux:table.rows>
                </flux:table>
                </div>
            </div>
        @endif
    </div>

    {{-- Modal: Taarifa za mwanafunzi na fomu ya malipo --}}
    <dialog id="student-payment-modal" class="w-full max-w-lg rounded-xl border-0 bg-white dark:bg-zinc-900 shadow-xl p-0 backdrop:bg-black/50 open:backdrop:backdrop-blur-sm focus:outline-none" onclick="if (event.target === this) this.close()">
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Taarifa na malipo') }}</h2>
                <button type="button" onclick="document.getElementById('student-payment-modal').close()" class="rounded-lg p-1.5 text-gray-400 hover:bg-gray-100 dark:hover:bg-zinc-800" aria-label="{{ __('Funga') }}">
                    <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div id="student-info-box" class="mb-4 p-4 rounded-lg bg-zinc-50 dark:bg-zinc-800/50 text-sm space-y-1">
                <p><strong>{{ __('Jina') }}:</strong> <span id="modal-fullname"></span></p>
                <p><strong>{{ __('Darasa') }}:</strong> <span id="modal-class"></span></p>
                <p><strong>{{ __('Mwaka') }}:</strong> <span id="modal-year"></span></p>
                <p><strong>{{ __('Simu') }}:</strong> <span id="modal-contact"></span></p>
                <p><strong>{{ __('Barua pepe') }}:</strong> <span id="modal-email"></span></p>
            </div>
            <form method="POST" action="{{ route('other-payments.store') }}" class="space-y-4">
                @csrf
                <input type="hidden" name="student_id" id="form-student-id" />
                <input type="hidden" name="term_number" id="form-term" />
                <input type="hidden" name="year" id="form-year" />
                <div>
                    <label for="payment_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Aina ya malipo') }}</label>
                    <select name="payment_type" id="payment_type" required class="block w-full rounded-xl border border-zinc-200/80 dark:border-zinc-600 dark:bg-zinc-800 dark:text-white text-sm px-3 py-2.5">
                        @foreach (\App\Models\PaymentTypeSetting::typeKeys() as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="amount" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Kiasi (TZS)') }}</label>
                    <flux:input
                        type="text"
                        name="amount"
                        id="amount"
                        value=""
                        required
                        inputmode="decimal"
                        placeholder="0.00"
                        variant="outline"
                        class="rounded-xl border-zinc-200/80 dark:border-white/10 w-full"
                    />
                </div>
                <div class="flex gap-3 justify-end pt-2">
                    <flux:button type="button" variant="outline" onclick="document.getElementById('student-payment-modal').close()" class="rounded-xl">{{ __('Funga') }}</flux:button>
                    <flux:button type="submit" variant="filled" color="blue" class="rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5">{{ __('Hifadhi malipo') }}</flux:button>
                </div>
            </form>
        </div>
    </dialog>
@endsection

@push('scripts')
    @if (!$students->isEmpty())
    <script>
        (function() {
            var tableBody = document.getElementById('other-payments-table-body');
            var searchInput = document.getElementById('op-search');
            var showingEl = document.getElementById('op-showing-count');
            var totalEl = document.getElementById('op-total-count');
            var total = {{ $students->count() }};

            function filterRows() {
                if (!tableBody) return;
                var q = (searchInput && searchInput.value || '').trim().toLowerCase();
                var rows = tableBody.querySelectorAll('tr');
                var visible = 0;
                var counter = 0;
                rows.forEach(function(row) {
                    var text = (row.textContent || '').toLowerCase();
                    var show = !q || text.indexOf(q) !== -1;
                    row.style.display = show ? '' : 'none';
                    if (show) {
                        visible++;
                        counter++;
                        var cells = row.querySelectorAll('td');
                        if (cells[0]) cells[0].textContent = counter;
                    }
                });
                if (showingEl) showingEl.textContent = visible;
            }

            function openModal(row) {
                var id = row.getAttribute('data-student-id');
                var fullname = row.getAttribute('data-fullname');
                var className = row.getAttribute('data-class-name');
                var year = row.getAttribute('data-year-val');
                var contact = row.getAttribute('data-contact');
                var email = row.getAttribute('data-email');
                var term = row.getAttribute('data-term');
                document.getElementById('modal-fullname').textContent = fullname || '—';
                document.getElementById('modal-class').textContent = className || '—';
                document.getElementById('modal-year').textContent = year || '—';
                document.getElementById('modal-contact').textContent = contact || '—';
                document.getElementById('modal-email').textContent = email || '—';
                document.getElementById('form-student-id').value = id;
                document.getElementById('form-term').value = term;
                document.getElementById('form-year').value = year;
                var modal = document.getElementById('student-payment-modal');
                var amountInput = modal ? modal.querySelector('input[name="amount"]') : null;
                if (amountInput) amountInput.value = '';
                document.getElementById('student-payment-modal').showModal();
            }

            document.addEventListener('DOMContentLoaded', function() {
                filterRows();
                if (tableBody) {
                    tableBody.querySelectorAll('tr').forEach(function(tr) {
                        tr.addEventListener('click', function() { openModal(tr); });
                    });
                }
                if (searchInput) searchInput.addEventListener('input', filterRows);
            });
        })();
    </script>
    @endif
@endpush
