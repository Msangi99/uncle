@extends('layouts.app')

@section('title', __('Students'))
@section('header', __('Students'))

@section('content')
    <div class="space-y-6 pb-8">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Orodha ya wanafunzi.') }}</p>
                <form method="GET" action="{{ route('students.index') }}" class="flex items-center gap-2 flex-wrap">
                    <label class="text-sm text-gray-600 dark:text-gray-400">{{ __('Mwaka') }}:</label>
                    <input type="text" name="year" value="{{ $year ?? date('Y') }}" placeholder="{{ date('Y') }}" class="rounded-lg border border-zinc-300 dark:border-zinc-600 dark:bg-zinc-800 dark:text-white px-2.5 py-1.5 text-sm w-20" />
                    <label class="text-sm text-gray-600 dark:text-gray-400">{{ __('Msimu') }}:</label>
                    <select name="term" class="rounded-lg border border-zinc-300 dark:border-zinc-600 dark:bg-zinc-800 dark:text-white px-2.5 py-1.5 text-sm">
                        @foreach (config('school.terms', []) as $t => $cfg)
                            <option value="{{ $t }}" {{ ($term ?? 1) == $t ? 'selected' : '' }}>{{ $cfg['label'] ?? "Msimu $t" }}</option>
                        @endforeach
                        @if (empty(config('school.terms')))
                            @for ($t = 1; $t <= 4; $t++)<option value="{{ $t }}" {{ ($term ?? 1) == $t ? 'selected' : '' }}>Msimu {{ $t }}</option>@endfor
                        @endif
                    </select>
                    <button type="submit" class="rounded-lg bg-zinc-200 dark:bg-zinc-700 px-3 py-1.5 text-sm">{{ __('Ona') }}</button>
                </form>
                <span class="text-xs text-gray-500 dark:text-gray-400">({{ __('Mstari mwekundu = ada chini ya mahitaji ya msimu; mahitaji yanahesabiwa kwa asilimia ya muhula kwenye ada yako (Day/Boarding).') }})</span>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" id="students-edit-btn" disabled class="inline-flex items-center gap-2 rounded-lg border border-gray-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 px-4 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-zinc-700 disabled:opacity-50 disabled:pointer-events-none" title="{{ __('Chagua mwanafunzi mmoja kuhariri') }}">{{ __('Hariri') }}</button>
                <button type="button" id="students-delete-btn" disabled class="inline-flex items-center gap-2 rounded-lg border border-red-300 dark:border-red-800 bg-white dark:bg-zinc-800 px-4 py-2.5 text-sm font-medium text-red-700 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 disabled:opacity-50 disabled:pointer-events-none" title="{{ __('Chagua wanafunzi kufuta') }}">{{ __('Futa') }}</button>
                <button type="button" onclick="document.getElementById('add-single-modal').showModal()" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 px-4 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-zinc-700">{{ __('Ongeza Mmoja') }}</button>
                <button type="button" onclick="document.getElementById('add-multiple-modal').showModal()" class="inline-flex items-center gap-2 rounded-lg bg-[#FF2D20] px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-[#e0281a]">{{ __('Ongeza Wengi (Excel)') }}</button>
            </div>
        </div>

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

        @if ($students->isEmpty())
            <div class="rounded-xl border-2 border-dashed border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-12 text-center">
                <p class="text-gray-500 dark:text-gray-400">{{ __('Hakuna wanafunzi bado. Tumia "Ongeza Mmoja" au "Ongeza Wengi" kuongeza.') }}</p>
            </div>
        @else
            <div id="students-table-wrapper" class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 overflow-hidden shadow-sm">
                {{-- DataTables-style toolbar --}}
                <div class="flex flex-wrap items-center justify-between gap-3 px-5 py-4 border-b border-zinc-200 dark:border-zinc-700 bg-zinc-50/80 dark:bg-zinc-800/50">
                    <div class="flex items-center gap-2 text-sm text-zinc-600 dark:text-zinc-400">
                        <span id="students-table-info">
                            {{ __('Ona') }} <strong id="students-showing-from">1</strong> {{ __('hadi') }} <strong id="students-visible-count">{{ $students->count() }}</strong> {{ __('kati ya') }} <strong id="students-total-count">{{ $students->count() }}</strong> {{ __('wanafunzi') }}
                        </span>
                    </div>
                    <div class="flex items-center gap-3">
                        <button
                            type="button"
                            id="students-export-btn"
                            class="inline-flex items-center gap-1.5 rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 px-3 py-2 text-sm font-medium text-zinc-700 dark:text-zinc-300 hover:bg-zinc-50 dark:hover:bg-zinc-700"
                            title="{{ __('Pakua Excel ya data iliyochujwa') }}"
                        >
                            <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            {{ __('Export') }}
                        </button>
                        <label class="flex items-center gap-2 text-sm text-zinc-600 dark:text-zinc-400">
                            <span>{{ __('Tafuta') }}:</span>
                            <input
                                type="search"
                                id="students-table-search"
                                placeholder="{{ __('Andika kutaftua...') }}"
                                class="rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 dark:text-white px-3 py-2 text-sm w-48 sm:w-56 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 dark:focus:border-blue-400 dark:focus:ring-blue-400"
                                autocomplete="off"
                            />
                        </label>
                    </div>
                </div>
                <div class="px-4 py-4">
                <flux:table class="dark:text-zinc-200">
                    <flux:table.columns>
                        <flux:table.column align="center" class="w-14">
                            <label class="inline-flex items-center cursor-pointer" title="{{ __('Chagua wote') }}">
                                <input type="checkbox" id="students-select-all" class="student-cb-all rounded border-zinc-300 dark:border-zinc-600 dark:bg-zinc-800" />
                            </label>
                        </flux:table.column>
                        <flux:table.column align="center" class="w-14">{{ __('#') }}</flux:table.column>
                        <flux:table.column>{{ __('Jina kamili') }}</flux:table.column>
                        <flux:table.column>{{ __('Darasa') }}</flux:table.column>
                        <flux:table.column>{{ __('Mwaka') }}</flux:table.column>
                        <flux:table.column>{{ __('Aina') }}</flux:table.column>
                        <flux:table.column>{{ __('Simu') }}</flux:table.column>
                        <flux:table.column>{{ __('Barua pepe') }}</flux:table.column>
                        <flux:table.column align="center">{{ __('Level ya malipo') }}</flux:table.column>
                    </flux:table.columns>
                    <flux:table.rows id="students-table-body">
                        @foreach ($students as $student)
                    <flux:table.row key="{{ $student->id }}" data-row-num="{{ $loop->iteration }}" data-student-id="{{ $student->id }}" data-fullname="{{ e($student->fullname) }}" data-class-id="{{ $student->class_id }}" data-year="{{ e($student->year) }}" data-student-type="{{ $student->student_type ?? 'day' }}" data-fee-amount="{{ $student->fee_amount !== null ? number_format($student->fee_amount, 0, '.', '') : '' }}" data-contact="{{ e($student->contact ?? '') }}" data-email="{{ e($student->email ?? '') }}" class="{{ ($student->below_required ?? false) ? '!bg-red-100 dark:!bg-red-900/30' : '!bg-green-50 dark:!bg-green-900/20' }}">
                                <flux:table.cell align="center">
                                    <input type="checkbox" class="student-row-cb rounded border-zinc-300 dark:border-zinc-600 dark:bg-zinc-800" value="{{ $student->id }}" />
                                </flux:table.cell>
                                <flux:table.cell align="center" class="text-zinc-500 dark:text-zinc-400">{{ $loop->iteration }}</flux:table.cell>
                                <flux:table.cell variant="strong" class="{{ ($student->below_required ?? false) ? '!text-red-700 dark:!text-red-300' : '' }}">{{ $student->fullname }}</flux:table.cell>
                                <flux:table.cell class="{{ ($student->below_required ?? false) ? '!text-red-700 dark:!text-red-300' : '' }}">{{ $student->classe->name }}</flux:table.cell>
                                <flux:table.cell class="{{ ($student->below_required ?? false) ? '!text-red-700 dark:!text-red-300' : '' }}">{{ $student->year }}</flux:table.cell>
                                <flux:table.cell class="{{ ($student->below_required ?? false) ? '!text-red-700 dark:!text-red-300' : '' }}">{{ ($student->student_type ?? 'day') === 'boarding' ? __('Boarding') : __('Day') }}</flux:table.cell>
                                <flux:table.cell class="{{ ($student->below_required ?? false) ? '!text-red-700 dark:!text-red-300' : '' }}">{{ $student->contact ?? '—' }}</flux:table.cell>
                                <flux:table.cell class="{{ ($student->below_required ?? false) ? '!text-red-700 dark:!text-red-300' : '' }}">{{ $student->email ?? '—' }}</flux:table.cell>
                                <flux:table.cell align="center" class="text-sm {{ ($student->below_required ?? false) ? '!text-red-700 dark:!text-red-300' : '' }}">
                                    @php $pl = $student->payment_level ?? []; $termsCfg = config('school.terms', []); @endphp
                                    <span class="inline-flex flex-wrap gap-1 justify-center" title="{{ __('Msimu 1–4: ✓ = imelipwa, ✗ = haijalipwa') }}">
                                        @for ($t = 1; $t <= 4; $t++)
                                            @php $short = $termsCfg[$t]['short'] ?? 'M'.$t; @endphp
                                            <span class="inline-flex items-center gap-0.5">{{ $short }}{{ ($pl[$t] ?? false) ? ' ✓' : ' ✗' }}</span>
                                        @endfor
                                    </span>
                                </flux:table.cell>
                            </flux:table.row>
                        @endforeach
                    </flux:table.rows>
                </flux:table>
                </div>
            </div>
        @endif
    </div>

    {{-- Modal: Add Single Student --}}
    <dialog id="add-single-modal" class="w-full max-w-md rounded-xl border-0 bg-white dark:bg-zinc-900 shadow-xl p-0 backdrop:bg-black/50 open:backdrop:backdrop-blur-sm focus:outline-none" onclick="if (event.target === this) this.close()">
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Ongeza Mwanafunzi Mmoja') }}</h2>
                <button type="button" onclick="document.getElementById('add-single-modal').close()" class="rounded-lg p-1.5 text-gray-400 hover:bg-gray-100 dark:hover:bg-zinc-800" aria-label="{{ __('Funga') }}">
                    <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form method="POST" action="{{ route('students.store') }}" class="space-y-5">
                @csrf
                <flux:input
                    type="text"
                    name="fullname"
                    label="{{ __('Jina kamili') }}"
                    placeholder="{{ __('Jina kamili la mwanafunzi') }}"
                    value="{{ old('fullname') }}"
                    required
                    autofocus
                    maxlength="255"
                    icon-leading="user"
                    variant="outline"
                    class="rounded-xl border-zinc-200/80 dark:border-white/10 focus-within:ring-2 focus-within:ring-blue-500/20 focus-within:border-blue-500 dark:focus-within:border-blue-400"
                />
                <div>
                    <label for="single-class" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Darasa') }}</label>
                    <select id="single-class" name="class_id" required class="block w-full rounded-xl border border-zinc-200/80 dark:border-white/10 dark:bg-zinc-800 dark:text-white shadow-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 text-sm px-3 py-2.5">
                        <option value="">{{ __('Chagua darasa') }}</option>
                        @foreach ($classes as $c)
                            <option value="{{ $c->id }}" {{ old('class_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="single-student-type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Aina') }} ({{ __('Day') }} / {{ __('Boarding') }})</label>
                    <select id="single-student-type" name="student_type" required class="block w-full rounded-xl border border-zinc-200/80 dark:border-white/10 dark:bg-zinc-800 dark:text-white shadow-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 text-sm px-3 py-2.5">
                        <option value="day" {{ old('student_type', 'day') === 'day' ? 'selected' : '' }}>{{ __('Day') }}</option>
                        <option value="boarding" {{ old('student_type') === 'boarding' ? 'selected' : '' }}>{{ __('Boarding') }}</option>
                    </select>
                </div>
                <flux:input
                    type="text"
                    name="fee_amount"
                    label="{{ __('Ada ya mwanafunzi (kwa mwaka, TZS)') }}"
                    placeholder="{{ __('mf. 500000') }}"
                    value="{{ old('fee_amount') }}"
                    required
                    inputmode="decimal"
                    variant="outline"
                    class="rounded-xl border-zinc-200/80 dark:border-white/10 focus-within:ring-2 focus-within:ring-blue-500/20 focus-within:border-blue-500 dark:focus-within:border-blue-400"
                />
                <flux:input
                    type="text"
                    name="year"
                    label="{{ __('Mwaka') }}"
                    placeholder="{{ __('mf. 2024') }}"
                    value="{{ old('year') }}"
                    required
                    maxlength="50"
                    variant="outline"
                    class="rounded-xl border-zinc-200/80 dark:border-white/10 focus-within:ring-2 focus-within:ring-blue-500/20 focus-within:border-blue-500 dark:focus-within:border-blue-400"
                />
                <flux:input
                    type="text"
                    name="contact"
                    label="{{ __('Simu / Contact') }}"
                    placeholder="{{ __('Nambari ya simu') }}"
                    value="{{ old('contact') }}"
                    maxlength="255"
                    variant="outline"
                    class="rounded-xl border-zinc-200/80 dark:border-white/10 focus-within:ring-2 focus-within:ring-blue-500/20 focus-within:border-blue-500 dark:focus-within:border-blue-400"
                />
                <flux:input
                    type="email"
                    name="email"
                    label="{{ __('Barua pepe') }}"
                    placeholder="{{ __('email@example.com') }}"
                    value="{{ old('email') }}"
                    maxlength="255"
                    variant="outline"
                    class="rounded-xl border-zinc-200/80 dark:border-white/10 focus-within:ring-2 focus-within:ring-blue-500/20 focus-within:border-blue-500 dark:focus-within:border-blue-400"
                />
                <div class="flex gap-3 justify-end pt-2">
                    <flux:button type="button" variant="outline" onclick="document.getElementById('add-single-modal').close()" class="rounded-xl border-zinc-200/80 dark:border-white/10">{{ __('Ghairi') }}</flux:button>
                    <flux:button type="submit" variant="filled" color="blue" class="rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5">{{ __('Hifadhi') }}</flux:button>
                </div>
            </form>
        </div>
    </dialog>

    {{-- Modal: Add Multiple (Excel) --}}
    <dialog id="add-multiple-modal" class="w-full max-w-md rounded-xl border-0 bg-white dark:bg-zinc-900 shadow-xl p-0 backdrop:bg-black/50 open:backdrop:backdrop-blur-sm focus:outline-none" onclick="if (event.target === this) this.close()">
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Ongeza Wanafunzi Wengi (Excel)') }}</h2>
                <button type="button" onclick="document.getElementById('add-multiple-modal').close()" class="rounded-lg p-1.5 text-gray-400 hover:bg-gray-100 dark:hover:bg-zinc-800" aria-label="{{ __('Funga') }}">
                    <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form method="POST" action="{{ route('students.import') }}" enctype="multipart/form-data" class="space-y-5">
                @csrf
                <div>
                    <label for="multi-class" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Darasa') }}</label>
                    <select id="multi-class" name="class_id" required class="block w-full rounded-xl border border-zinc-200/80 dark:border-white/10 dark:bg-zinc-800 dark:text-white shadow-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 text-sm px-3 py-2.5">
                        <option value="">{{ __('Chagua darasa') }}</option>
                        @foreach ($classes as $c)
                            <option value="{{ $c->id }}" {{ old('class_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <flux:input
                    type="text"
                    name="year"
                    label="{{ __('Mwaka') }}"
                    placeholder="{{ __('mf. 2024') }}"
                    value="{{ old('year') }}"
                    required
                    maxlength="50"
                    variant="outline"
                    class="rounded-xl border-zinc-200/80 dark:border-white/10 focus-within:ring-2 focus-within:ring-blue-500/20 focus-within:border-blue-500 dark:focus-within:border-blue-400"
                />
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Faili la Excel') }}</label>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">{{ __('Safu: Jina kamili, Simu (contact), Barua pepe (email), Aina (day au boarding), Ada (fee_amount kwa mwaka, TZS).') }}</p>
                    <a href="{{ route('students.sample') }}" class="inline-flex items-center gap-1.5 text-sm text-blue-600 dark:text-blue-400 hover:underline mb-2">
                        <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        {{ __('Pakua sample Excel') }}
                    </a>
                    <input type="file" name="file" accept=".xlsx,.xls" required class="block w-full text-sm text-gray-500 file:mr-4 file:rounded-lg file:border-0 file:bg-blue-50 file:px-4 file:py-2 file:text-blue-600 dark:file:bg-blue-900/30 dark:file:text-blue-400 file:font-medium" />
                </div>
                <div class="flex gap-3 justify-end pt-2">
                    <flux:button type="button" variant="outline" onclick="document.getElementById('add-multiple-modal').close()" class="rounded-xl border-zinc-200/80 dark:border-white/10">{{ __('Ghairi') }}</flux:button>
                    <flux:button type="submit" variant="filled" color="blue" class="rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5">{{ __('Ingiza') }}</flux:button>
                </div>
            </form>
        </div>
    </dialog>

    {{-- Modal: Edit Student --}}
    <dialog id="edit-student-modal" class="w-full max-w-md rounded-xl border-0 bg-white dark:bg-zinc-900 shadow-xl p-0 backdrop:bg-black/50 open:backdrop:backdrop-blur-sm focus:outline-none" onclick="if (event.target === this) this.close()">
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Hariri Mwanafunzi') }}</h2>
                <button type="button" onclick="document.getElementById('edit-student-modal').close()" class="rounded-lg p-1.5 text-gray-400 hover:bg-gray-100 dark:hover:bg-zinc-800" aria-label="{{ __('Funga') }}">
                    <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form id="edit-student-form" method="POST" action="" class="space-y-5">
                @csrf
                @method('PUT')
                <flux:input type="text" name="fullname" id="edit-fullname" label="{{ __('Jina kamili') }}" required maxlength="255" variant="outline" class="rounded-xl border-zinc-200/80 dark:border-white/10" />
                <div>
                    <label for="edit-class_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Darasa') }}</label>
                    <select id="edit-class_id" name="class_id" required class="block w-full rounded-xl border border-zinc-200/80 dark:border-white/10 dark:bg-zinc-800 dark:text-white shadow-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 text-sm px-3 py-2.5">
                        @foreach ($classes as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="edit-student_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Aina') }} ({{ __('Day') }} / {{ __('Boarding') }})</label>
                    <select id="edit-student_type" name="student_type" required class="block w-full rounded-xl border border-zinc-200/80 dark:border-white/10 dark:bg-zinc-800 dark:text-white shadow-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 text-sm px-3 py-2.5">
                        <option value="day">{{ __('Day') }}</option>
                        <option value="boarding">{{ __('Boarding') }}</option>
                    </select>
                </div>
                <flux:input type="text" name="fee_amount" id="edit-fee_amount" label="{{ __('Ada ya mwanafunzi (kwa mwaka, TZS)') }}" required inputmode="decimal" variant="outline" class="rounded-xl border-zinc-200/80 dark:border-white/10" />
                <flux:input type="text" name="year" id="edit-year" label="{{ __('Mwaka') }}" required maxlength="50" variant="outline" class="rounded-xl border-zinc-200/80 dark:border-white/10" />
                <flux:input type="text" name="contact" id="edit-contact" label="{{ __('Simu / Contact') }}" maxlength="255" variant="outline" class="rounded-xl border-zinc-200/80 dark:border-white/10" />
                <flux:input type="email" name="email" id="edit-email" label="{{ __('Barua pepe') }}" maxlength="255" variant="outline" class="rounded-xl border-zinc-200/80 dark:border-white/10" />
                <div class="flex gap-3 justify-end pt-2">
                    <flux:button type="button" variant="outline" onclick="document.getElementById('edit-student-modal').close()" class="rounded-xl">{{ __('Ghairi') }}</flux:button>
                    <flux:button type="submit" variant="filled" color="blue" class="rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5">{{ __('Hifadhi') }}</flux:button>
                </div>
            </form>
        </div>
    </dialog>

    <form id="students-bulk-delete-form" method="POST" action="{{ route('students.bulk-destroy') }}" class="hidden">
        @csrf
    </form>

    @if (!$students->isEmpty())
        <script>
            (function() {
                var searchInput = document.getElementById('students-table-search');
                var tableBody = document.getElementById('students-table-body');
                var visibleCountEl = document.getElementById('students-visible-count');
                var selectAll = document.getElementById('students-select-all');
                var editBtn = document.getElementById('students-edit-btn');
                var deleteBtn = document.getElementById('students-delete-btn');
                var bulkDeleteForm = document.getElementById('students-bulk-delete-form');

                function getSelectedRows() {
                    if (!tableBody) return [];
                    var cbs = tableBody.querySelectorAll('.student-row-cb:checked');
                    return Array.from(cbs).map(function(cb) { return cb.closest('tr'); }).filter(Boolean);
                }

                function updateActionButtons() {
                    var rows = getSelectedRows();
                    var n = rows.length;
                    if (editBtn) {
                        editBtn.disabled = n !== 1;
                    }
                    if (deleteBtn) {
                        deleteBtn.disabled = n === 0;
                    }
                    if (selectAll && tableBody) {
                        var all = tableBody.querySelectorAll('.student-row-cb');
                        var visible = tableBody.querySelectorAll('tr');
                        var visibleCbs = Array.from(visible).filter(function(r) { return r.style.display !== 'none'; }).map(function(r) { return r.querySelector('.student-row-cb'); }).filter(Boolean);
                        selectAll.checked = visibleCbs.length > 0 && visibleCbs.every(function(cb) { return cb.checked; });
                        selectAll.indeterminate = visibleCbs.some(function(cb) { return cb.checked; }) && !selectAll.checked;
                    }
                }

                if (selectAll && tableBody) {
                    selectAll.addEventListener('change', function() {
                        tableBody.querySelectorAll('.student-row-cb').forEach(function(cb) {
                            var row = cb.closest('tr');
                            if (row && row.style.display !== 'none') cb.checked = selectAll.checked;
                        });
                        updateActionButtons();
                    });
                }
                if (tableBody) {
                    tableBody.addEventListener('change', function() { updateActionButtons(); });
                }

                if (editBtn && tableBody) {
                    editBtn.addEventListener('click', function() {
                        var rows = getSelectedRows();
                        if (rows.length !== 1) return;
                        var row = rows[0];
                        var id = row.getAttribute('data-student-id');
                        var form = document.getElementById('edit-student-form');
                        form.action = '{{ url('students') }}/' + id;
                        var setVal = function(name, val) {
                            var el = form.querySelector('[name="' + name + '"]');
                            if (el) el.value = val || '';
                        };
                        setVal('fullname', row.getAttribute('data-fullname'));
                        setVal('class_id', row.getAttribute('data-class-id'));
                        setVal('year', row.getAttribute('data-year'));
                        setVal('student_type', row.getAttribute('data-student-type') || 'day');
                        setVal('fee_amount', row.getAttribute('data-fee-amount'));
                        setVal('contact', row.getAttribute('data-contact'));
                        setVal('email', row.getAttribute('data-email'));
                        document.getElementById('edit-student-modal').showModal();
                    });
                }

                if (deleteBtn && bulkDeleteForm) {
                    deleteBtn.addEventListener('click', function() {
                        var ids = Array.from(tableBody.querySelectorAll('.student-row-cb:checked')).map(function(cb) { return cb.value; });
                        if (ids.length === 0) return;
                        if (!confirm('{{ __("Una uhakika unataka kufuta wanafunzi") }} ' + ids.length + '?')) return;
                        bulkDeleteForm.innerHTML = '';
                        var csrf = document.createElement('input');
                        csrf.type = 'hidden'; csrf.name = '_token'; csrf.value = '{{ csrf_token() }}';
                        bulkDeleteForm.appendChild(csrf);
                        ids.forEach(function(id) {
                            var inp = document.createElement('input');
                            inp.type = 'hidden'; inp.name = 'student_ids[]'; inp.value = id;
                            bulkDeleteForm.appendChild(inp);
                        });
                        bulkDeleteForm.submit();
                    });
                }

                function filterTable() {
                    if (!searchInput || !tableBody) return;
                    var query = (searchInput.value || '').trim().toLowerCase();
                    var rows = tableBody.querySelectorAll('tr');
                    var visible = 0;
                    var firstShown = 0;
                    var counter = 0;
                    rows.forEach(function(row, index) {
                        var text = (row.textContent || '').toLowerCase();
                        var show = !query || text.indexOf(query) !== -1;
                        row.style.display = show ? '' : 'none';
                        if (show) {
                            visible++;
                            counter++;
                            if (firstShown === 0) firstShown = counter;
                            var cells = row.querySelectorAll('td');
                            if (cells[1]) cells[1].textContent = counter;
                        }
                    });
                    if (visibleCountEl) visibleCountEl.textContent = visible;
                    var fromEl = document.getElementById('students-showing-from');
                    if (fromEl) fromEl.textContent = visible ? firstShown : 0;
                }

                document.addEventListener('DOMContentLoaded', function() {
                    filterTable();
                    updateActionButtons();
                });
                if (searchInput) {
                    searchInput.addEventListener('input', filterTable);
                    searchInput.addEventListener('keyup', filterTable);
                }

                var exportBtn = document.getElementById('students-export-btn');
                if (exportBtn && searchInput) {
                    exportBtn.addEventListener('click', function() {
                        var q = (searchInput.value || '').trim();
                        var url = '{{ route('students.export') }}';
                        if (q) url += '?search=' + encodeURIComponent(q);
                        window.location.href = url;
                    });
                }
            })();
        </script>
    @endif
@endsection
