@extends('layouts.app')

@section('title', __('Classes'))
@section('header', __('Classes'))

@section('content')
    <div class="space-y-6">
        {{-- Top bar: title + Add Class button --}}
        <div class="flex flex-wrap items-center justify-between gap-4">
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Orodha ya madarasa.') }}</p>
            <button
                type="button"
                onclick="document.getElementById('add-class-modal').showModal()"
                class="inline-flex items-center gap-2 rounded-lg bg-[#FF2D20] px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-[#e0281a] focus:outline-none focus:ring-2 focus:ring-[#FF2D20] focus:ring-offset-2 dark:focus:ring-offset-zinc-950"
            >
                <span aria-hidden="true">+</span>
                {{ __('Ongeza Darasa') }}
            </button>
        </div>

        @if (session('success'))
            <div class="rounded-lg bg-green-50 dark:bg-green-900/20 px-4 py-3 text-sm text-green-700 dark:text-green-400">
                {{ session('success') }}
            </div>
        @endif

        {{-- Classes grid: 4 per row --}}
        @if ($classes->isEmpty())
            <div class="rounded-xl border-2 border-dashed border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-12 text-center">
                <p class="text-gray-500 dark:text-gray-400">{{ __('Hakuna madarasa bado. Bofya "Ongeza Darasa" kuongeza.') }}</p>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                @foreach ($classes as $classe)
                    <div class="rounded-xl border border-gray-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 shadow-sm overflow-hidden transition hover:shadow-md hover:ring-1 hover:ring-[#FF2D20]/30">
                        <div class="p-5">
                            <div class="flex items-start justify-between gap-2">
                                <div class="flex items-center gap-3 min-w-0">
                                    <span class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-[#FF2D20]/10 text-[#FF2D20] text-lg">📚</span>
                                    <h3 class="font-semibold text-gray-900 dark:text-white truncate">{{ $classe->name }}</h3>
                                </div>
                                <div class="flex items-center gap-1 shrink-0">
                                    <button
                                        type="button"
                                        onclick="openEditModal({{ $classe->id }}, {{ json_encode($classe->name) }})"
                                        class="rounded-lg p-2 text-gray-500 hover:bg-gray-100 hover:text-blue-600 dark:hover:bg-zinc-700 dark:hover:text-blue-400"
                                        title="{{ __('Hariri') }}"
                                    >
                                        <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                    <form method="POST" action="{{ route('classes.destroy', $classe) }}" class="inline" onsubmit="return confirm('{{ __('Una uhakika unataka kufuta darasa hili?') }}');">
                                        @csrf
                                        @method('DELETE')
                                        <button
                                            type="submit"
                                            class="rounded-lg p-2 text-gray-500 hover:bg-gray-100 hover:text-red-600 dark:hover:bg-zinc-700 dark:hover:text-red-400"
                                            title="{{ __('Futa') }}"
                                        >
                                            <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Modal: Add Class form (class name only) --}}
    <dialog id="add-class-modal" class="w-full max-w-md rounded-xl border-0 bg-white dark:bg-zinc-900 shadow-xl p-0 backdrop:bg-black/50 open:backdrop:backdrop-blur-sm focus:outline-none" onclick="if (event.target === this) this.close()">
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Ongeza Darasa') }}</h2>
                <button type="button" onclick="document.getElementById('add-class-modal').close()" class="rounded-lg p-1.5 text-gray-400 hover:bg-gray-100 hover:text-gray-600 dark:hover:bg-zinc-800 dark:hover:text-gray-300" aria-label="{{ __('Funga') }}">
                    <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form method="POST" action="{{ route('classes.store') }}" class="space-y-5">
                @csrf
                <flux:input
                    type="text"
                    name="name"
                    label="{{ __('Jina la darasa') }}"
                    placeholder="{{ __('mf. Form 1, Grade 2') }}"
                    value="{{ old('name') }}"
                    required
                    autofocus
                    maxlength="255"
                    icon-leading="academic-cap"
                    variant="outline"
                    class="rounded-xl border-zinc-200/80 dark:border-white/10 focus-within:ring-2 focus-within:ring-blue-500/20 focus-within:border-blue-500 dark:focus-within:border-blue-400"
                />
                @error('name')
                    <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
                <div class="flex gap-3 justify-end pt-2">
                    <flux:button
                        type="button"
                        variant="outline"
                        onclick="document.getElementById('add-class-modal').close()"
                        class="rounded-xl border-zinc-200/80 dark:border-white/10"
                    >
                        {{ __('Ghairi') }}
                    </flux:button>
                    <flux:button
                        type="submit"
                        variant="filled"
                        color="blue"
                        class="rounded-xl bg-blue-600 hover:bg-blue-700 dark:bg-blue-600 dark:hover:bg-blue-500 text-white font-semibold shadow-sm focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-zinc-900 py-2.5"
                    >
                        {{ __('Hifadhi') }}
                    </flux:button>
                </div>
            </form>
        </div>
    </dialog>

    {{-- Modal: Edit Class --}}
    <dialog id="edit-class-modal" class="w-full max-w-md rounded-xl border-0 bg-white dark:bg-zinc-900 shadow-xl p-0 backdrop:bg-black/50 open:backdrop:backdrop-blur-sm focus:outline-none" onclick="if (event.target === this) this.close()">
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Hariri Darasa') }}</h2>
                <button type="button" onclick="document.getElementById('edit-class-modal').close()" class="rounded-lg p-1.5 text-gray-400 hover:bg-gray-100 hover:text-gray-600 dark:hover:bg-zinc-800 dark:hover:text-gray-300" aria-label="{{ __('Funga') }}">
                    <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form id="edit-class-form" method="POST" action="" class="space-y-5">
                @csrf
                @method('PUT')
                <flux:input
                    type="text"
                    name="name"
                    id="edit-class-name"
                    label="{{ __('Jina la darasa') }}"
                    placeholder="{{ __('mf. Form 1, Grade 2') }}"
                    required
                    maxlength="255"
                    icon-leading="academic-cap"
                    variant="outline"
                    class="rounded-xl border-zinc-200/80 dark:border-white/10 focus-within:ring-2 focus-within:ring-blue-500/20 focus-within:border-blue-500 dark:focus-within:border-blue-400"
                />
                @error('name')
                    <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
                <div class="flex gap-3 justify-end pt-2">
                    <flux:button
                        type="button"
                        variant="outline"
                        onclick="document.getElementById('edit-class-modal').close()"
                        class="rounded-xl border-zinc-200/80 dark:border-white/10"
                    >
                        {{ __('Ghairi') }}
                    </flux:button>
                    <flux:button
                        type="submit"
                        variant="filled"
                        color="blue"
                        class="rounded-xl bg-blue-600 hover:bg-blue-700 dark:bg-blue-600 dark:hover:bg-blue-500 text-white font-semibold shadow-sm focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-zinc-900 py-2.5"
                    >
                        {{ __('Hifadhi') }}
                    </flux:button>
                </div>
            </form>
        </div>
    </dialog>

    <script>
        function openEditModal(id, name) {
            document.getElementById('edit-class-form').action = '{{ url('classes') }}/' + id;
            var inp = document.querySelector('#edit-class-modal input[name="name"]');
            if (inp) inp.value = name;
            document.getElementById('edit-class-modal').showModal();
        }
    </script>

    @if ($errors->has('name'))
        <script>
            @if (session('edit_id'))
                document.getElementById('edit-class-form').action = '{{ url('classes') }}/' + {{ session('edit_id') }};
                var inp = document.querySelector('#edit-class-modal input[name="name"]');
                if (inp) inp.value = {{ json_encode(old('name')) }};
                document.getElementById('edit-class-modal').showModal();
            @else
                document.getElementById('add-class-modal').showModal();
            @endif
        </script>
    @endif
@endsection
