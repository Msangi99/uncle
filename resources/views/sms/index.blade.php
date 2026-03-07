@extends('layouts.app')

@section('title', __('SMS'))
@section('header', __('SMS'))

@section('content')
    <div class="space-y-6 pb-8">
        {{-- Tabs --}}
        <div class="border-b border-zinc-200 dark:border-zinc-700">
            <nav class="flex gap-1" aria-label="Tabs">
                <button
                    type="button"
                    id="tab-ada"
                    data-tab="ada"
                    class="sms-tab rounded-t-lg px-4 py-2.5 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300"
                >
                    {{ __('SMS za Ada') }}
                </button>
                <button
                    type="button"
                    id="tab-matangazo"
                    data-tab="matangazo"
                    class="sms-tab rounded-t-lg px-4 py-2.5 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300"
                >
                    {{ __('Matangazo') }}
                </button>
            </nav>
        </div>

        @if (session('sms_result'))
            @php $r = session('sms_result'); @endphp
            <div class="rounded-lg px-4 py-3 text-sm {{ $r['success'] ? 'bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400' : 'bg-amber-50 dark:bg-amber-900/20 text-amber-700 dark:text-amber-400' }}">
                {{ $r['message'] }}
                @if (!empty($r['errors']))
                    <ul class="mt-2 list-disc list-inside text-xs opacity-90">
                        @foreach (array_slice($r['errors'], 0, 5) as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                        @if (count($r['errors']) > 5)
                            <li>{{ __('... na mengine :n', ['n' => count($r['errors']) - 5]) }}</li>
                        @endif
                    </ul>
                @endif
            </div>
        @endif

        {{-- Tab: Ada --}}
        <div id="panel-ada" class="sms-panel space-y-4">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <form method="GET" action="{{ route('sms.index') }}" class="flex items-center gap-2 flex-wrap">
                    <label class="text-sm text-gray-600 dark:text-gray-400">{{ __('Mwaka') }}:</label>
                    <input type="text" name="year" value="{{ $year ?? date('Y') }}" placeholder="{{ date('Y') }}" class="rounded-lg border border-zinc-300 dark:border-zinc-600 dark:bg-zinc-800 dark:text-white px-2.5 py-1.5 text-sm w-20" />
                    <label class="text-sm text-gray-600 dark:text-gray-400">{{ __('Darasa') }}:</label>
                    <select name="class_id" class="rounded-lg border border-zinc-300 dark:border-zinc-600 dark:bg-zinc-800 dark:text-white px-2.5 py-1.5 text-sm">
                        <option value="">{{ __('Zote') }}</option>
                        @foreach ($classes as $c)
                            <option value="{{ $c->id }}" {{ (isset($classId) && $classId == $c->id) ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="rounded-lg bg-zinc-200 dark:bg-zinc-700 px-3 py-1.5 text-sm">{{ __('Chagua') }}</button>
                </form>
                <div class="flex items-center gap-2">
                    <button
                        type="button"
                        id="sms-send-btn"
                        disabled
                        class="inline-flex items-center gap-2 rounded-lg bg-[#FF2D20] px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-[#e0281a] disabled:opacity-50 disabled:pointer-events-none"
                    >
                        📱 {{ __('Tuma SMS') }}
                    </button>
                </div>
            </div>

            @if ($students->isEmpty())
                <div class="rounded-xl border-2 border-dashed border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-12 text-center">
                    <p class="text-gray-500 dark:text-gray-400">{{ __('Hakuna wanafunzi kwa filter uliyochagua. Badilisha mwaka/darasa.') }}</p>
                </div>
            @else
                <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('Chagua wanafunzi wote (kwa filter uliyochagua) au chagua moja-moja. SMS zitatumwa kwa wenye nambari ya simu; wasio na simu utaona taarifa.') }}</p>
                <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 overflow-hidden shadow-sm">
                    <div class="px-4 py-4">
                        <flux:table class="dark:text-zinc-200">
                            <flux:table.columns>
                                <flux:table.column align="center" class="w-14">
                                    <label class="inline-flex items-center gap-1.5 cursor-pointer" title="{{ __('Chagua wote kwenye orodha hii') }}">
                                        <input type="checkbox" id="sms-select-all" class="rounded border-zinc-300 dark:border-zinc-600 dark:bg-zinc-800" />
                                        <span class="text-xs text-zinc-600 dark:text-zinc-400">{{ __('Chagua wote') }}</span>
                                    </label>
                                </flux:table.column>
                                <flux:table.column align="center" class="w-14">{{ __('#') }}</flux:table.column>
                                <flux:table.column>{{ __('Jina kamili') }}</flux:table.column>
                                <flux:table.column>{{ __('Darasa') }}</flux:table.column>
                                <flux:table.column>{{ __('Mwaka') }}</flux:table.column>
                                <flux:table.column>{{ __('Simu') }}</flux:table.column>
                            </flux:table.columns>
                            <flux:table.rows id="sms-table-body">
                                @foreach ($students as $idx => $student)
                                    <flux:table.row key="{{ $student->id }}" class="{{ !($student->contact && trim($student->contact)) ? 'opacity-75' : '' }}">
                                        <flux:table.cell align="center">
                                            <input type="checkbox" name="student_ids[]" value="{{ $student->id }}" class="sms-student-cb rounded border-zinc-300 dark:border-zinc-600 dark:bg-zinc-800" {{ $student->contact && trim($student->contact) ? '' : 'title="' . __('Hakuna simu') . '"' }} />
                                        </flux:table.cell>
                                        <flux:table.cell align="center" class="text-zinc-500 dark:text-zinc-400">{{ $idx + 1 }}</flux:table.cell>
                                        <flux:table.cell variant="strong">{{ $student->fullname }}</flux:table.cell>
                                        <flux:table.cell>{{ $student->classe->name }}</flux:table.cell>
                                        <flux:table.cell>{{ $student->year }}</flux:table.cell>
                                        <flux:table.cell>{{ $student->contact ?: '—' }}</flux:table.cell>
                                    </flux:table.row>
                                @endforeach
                            </flux:table.rows>
                        </flux:table>
                    </div>
                </div>
            @endif
        </div>

        {{-- Tab: Matangazo (placeholder) --}}
        <div id="panel-matangazo" class="sms-panel hidden">
            <div class="rounded-xl border-2 border-dashed border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-12 text-center">
                <p class="text-gray-500 dark:text-gray-400">{{ __('Matangazo – inafanyiwa kazi. Tumia kichupo "SMS za Ada" kwa sasa.') }}</p>
            </div>
        </div>
    </div>

    {{-- Modal: Compose SMS --}}
    <dialog id="sms-compose-modal" class="w-full max-w-lg rounded-xl border-0 bg-white dark:bg-zinc-900 shadow-xl p-0 backdrop:bg-black/50 open:backdrop:backdrop-blur-sm focus:outline-none" onclick="if (event.target === this) this.close()">
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Andika SMS') }}</h2>
                <button type="button" onclick="document.getElementById('sms-compose-modal').close()" class="rounded-lg p-1.5 text-gray-400 hover:bg-gray-100 dark:hover:bg-zinc-800" aria-label="{{ __('Funga') }}">
                    <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <p class="text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Vigezo unaweza kutumia (andika @ ili kuchagua au onyesha hapa chini):') }}</p>
            <ul class="text-xs text-gray-600 dark:text-gray-400 mb-3 space-y-1 list-none pl-0">
                <li><code class="px-1.5 py-0.5 rounded bg-zinc-200 dark:bg-zinc-700">@studentname</code> — {{ __('Jina la mwanafunzi') }}</li>
                <li><code class="px-1.5 py-0.5 rounded bg-zinc-200 dark:bg-zinc-700">@class</code> / <code class="px-1.5 py-0.5 rounded bg-zinc-200 dark:bg-zinc-700">@darasa</code> — {{ __('Darasa') }}</li>
                <li><code class="px-1.5 py-0.5 rounded bg-zinc-200 dark:bg-zinc-700">@year</code> / <code class="px-1.5 py-0.5 rounded bg-zinc-200 dark:bg-zinc-700">@mwaka</code> — {{ __('Mwaka') }}</li>
            </ul>
            <form id="sms-send-form" method="POST" action="{{ route('sms.send') }}" class="mt-4">
                @csrf
                <input type="hidden" name="student_ids" id="sms-student-ids-input" value="" />
                <div class="relative">
                    <textarea id="sms-message" name="message" rows="5" class="block w-full rounded-xl border border-zinc-200 dark:border-zinc-600 dark:bg-zinc-800 dark:text-white px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="{{ __('Mf: Mzazi wa @studentname, ada ya mwanafunzi ...') }}"></textarea>
                    <div id="sms-variable-list" class="hidden absolute z-20 left-0 top-full mt-1 w-56 rounded-lg border border-zinc-200 dark:border-zinc-600 bg-white dark:bg-zinc-800 shadow-lg py-1 max-h-48 overflow-y-auto"></div>
                </div>
                <div class="flex justify-end gap-2 mt-4">
                    <flux:button type="button" variant="outline" onclick="document.getElementById('sms-compose-modal').close()" class="rounded-xl">{{ __('Ghairi') }}</flux:button>
                    <flux:button type="submit" variant="filled" color="blue" class="rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5">{{ __('Tuma') }}</flux:button>
                </div>
            </form>
        </div>
    </dialog>
@endsection

@push('scripts')
<script>
(function() {
    var variables = [
        { key: '@studentname', label: '{{ __("Jina la mwanafunzi") }}' },
        { key: '@class', label: '{{ __("Darasa") }}' },
        { key: '@year', label: '{{ __("Mwaka") }}' },
        { key: '@darasa', label: '{{ __("Darasa") }}' },
        { key: '@mwaka', label: '{{ __("Mwaka") }}' }
    ];

    // Tabs
    document.querySelectorAll('.sms-tab').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var t = this.getAttribute('data-tab');
            document.querySelectorAll('.sms-tab').forEach(function(b) {
                b.classList.remove('border-[#FF2D20]', 'text-[#FF2D20]', 'dark:border-[#FF2D20]');
                b.classList.add('border-transparent', 'text-gray-500', 'dark:text-gray-400');
            });
            this.classList.remove('border-transparent', 'text-gray-500', 'dark:text-gray-400');
            this.classList.add('border-[#FF2D20]', 'text-[#FF2D20]');
            document.querySelectorAll('.sms-panel').forEach(function(p) {
                p.classList.add('hidden');
            });
            var panel = document.getElementById('panel-' + t);
            if (panel) panel.classList.remove('hidden');
        });
    });
    document.getElementById('tab-ada').click();

    // Select all
    var selectAll = document.getElementById('sms-select-all');
    var checkboxes = document.querySelectorAll('.sms-student-cb');
    if (selectAll) {
        selectAll.addEventListener('change', function() {
            checkboxes.forEach(function(cb) {
                cb.checked = selectAll.checked;
            });
            updateSmsButton();
        });
    }
    checkboxes.forEach(function(cb) {
        cb.addEventListener('change', updateSmsButton);
    });

    function updateSmsButton() {
        var n = document.querySelectorAll('.sms-student-cb:checked').length;
        var btn = document.getElementById('sms-send-btn');
        if (btn) {
            btn.disabled = n === 0;
            btn.title = n ? (n + ' {{ __("waliochaguliwa") }}') : '{{ __("Chagua wanafunzi kwanza") }}';
        }
    }
    updateSmsButton();

    // Open compose modal
    document.getElementById('sms-send-btn').addEventListener('click', function() {
        var ids = Array.from(document.querySelectorAll('.sms-student-cb:checked')).map(function(cb) { return cb.value; });
        if (ids.length === 0) return;
        document.getElementById('sms-student-ids-input').value = JSON.stringify(ids);
        document.getElementById('sms-message').value = '';
        document.getElementById('sms-compose-modal').showModal();
    });

    // Fix form: backend expects student_ids[] as array (add hidden inputs before submit)
    document.getElementById('sms-send-form').addEventListener('submit', function(e) {
        var raw = document.getElementById('sms-student-ids-input').value || '[]';
        var ids = [];
        try { ids = JSON.parse(raw); } catch (err) { ids = []; }
        document.getElementById('sms-student-ids-input').removeAttribute('name');
        ids.forEach(function(id) {
            var inp = document.createElement('input');
            inp.type = 'hidden';
            inp.name = 'student_ids[]';
            inp.value = id;
            this.appendChild(inp);
        }.bind(this));
    });

    // @ variable dropdown
    var msgEl = document.getElementById('sms-message');
    var listEl = document.getElementById('sms-variable-list');
    var atIndex = -1;
    var filter = '';

    function showVariableList(startIndex) {
        atIndex = startIndex;
        filter = msgEl.value.slice(startIndex + 1).toLowerCase().replace(/\s.*/, '');
        var html = '';
        variables.forEach(function(v) {
            var keyLower = v.key.toLowerCase();
            if (!keyLower.startsWith('@' + filter)) return;
            html += '<button type="button" class="sms-var-option w-full text-left px-3 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-zinc-100 dark:hover:bg-zinc-700" data-key="' + v.key.replace(/"/g, '&quot;') + '">' + v.key + ' <span class="text-gray-400 text-xs">(' + label + ')</span></button>';
        });
        if (!html) html = '<div class="px-3 py-2 text-sm text-gray-500">{{ __("Hakuna kigezo") }}</div>';
        listEl.innerHTML = html;
        listEl.classList.remove('hidden');
        listEl.querySelectorAll('.sms-var-option').forEach(function(opt, i) {
            opt.addEventListener('click', function() {
                var key = this.getAttribute('data-key');
                var before = msgEl.value.slice(0, atIndex);
                var after = msgEl.value.slice(msgEl.selectionStart || msgEl.value.length);
                msgEl.value = before + key + ' ' + after;
                listEl.classList.add('hidden');
                msgEl.focus();
            });
        });
    }

    function hideVariableList() {
        listEl.classList.add('hidden');
    }

    msgEl.addEventListener('input', function() {
        var pos = this.selectionStart;
        var text = this.value.slice(0, pos);
        var lastAt = text.lastIndexOf('@');
        if (lastAt === -1) {
            hideVariableList();
            return;
        }
        var afterAt = text.slice(lastAt + 1);
        if (/[\s,.]/.test(afterAt) || afterAt.length > 20) {
            hideVariableList();
            return;
        }
        showVariableList(lastAt);
    });

    msgEl.addEventListener('blur', function() {
        setTimeout(hideVariableList, 200);
    });
})();
</script>
@endpush