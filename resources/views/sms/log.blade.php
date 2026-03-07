@extends('layouts.app')

@section('title', __('Historia ya SMS'))
@section('header', __('Historia ya SMS'))

@section('content')
    <div class="space-y-6 pb-8">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Orodha ya ujumbe ulizotuma na status zake.') }}</p>
            @if (!$logs->isEmpty())
                <form method="POST" action="{{ route('sms.log.clear') }}" class="inline" onsubmit="return confirm('{{ __('Una uhakika unataka kufuta historia yote ya SMS? Huu hatua hauwezi kutenduliwa.') }}');">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-2 rounded-lg border border-red-300 dark:border-red-800 bg-white dark:bg-zinc-800 px-4 py-2 text-sm font-medium text-red-700 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20">
                        {{ __('Futa historia yote') }}
                    </button>
                </form>
            @endif
        </div>

        @if (session('success'))
            <div class="rounded-lg bg-green-50 dark:bg-green-900/20 px-4 py-3 text-sm text-green-700 dark:text-green-400">
                {{ session('success') }}
            </div>
        @endif

        @if ($logs->isEmpty())
            <div class="rounded-xl border-2 border-dashed border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-12 text-center">
                <p class="text-gray-500 dark:text-gray-400">{{ __('Bado hakuna ujumbe uliotumwa.') }}</p>
            </div>
        @else
            <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 overflow-hidden shadow-sm">
                <div class="overflow-x-auto">
                    <flux:table class="dark:text-zinc-200">
                        <flux:table.columns>
                            <flux:table.column>{{ __('Tarehe / Saa') }}</flux:table.column>
                            <flux:table.column>{{ __('Mwanafunzi') }}</flux:table.column>
                            <flux:table.column>{{ __('Simu') }}</flux:table.column>
                            <flux:table.column>{{ __('Ujumbe') }}</flux:table.column>
                            <flux:table.column align="center">{{ __('Status') }}</flux:table.column>
                            <flux:table.column>{{ __('Maelezo') }}</flux:table.column>
                        </flux:table.columns>
                        <flux:table.rows>
                            @foreach ($logs as $log)
                                <flux:table.row>
                                    <flux:table.cell class="text-sm text-zinc-600 dark:text-zinc-400 whitespace-nowrap">
                                        {{ $log->sent_at->format('d/m/Y H:i') }}
                                    </flux:table.cell>
                                    <flux:table.cell variant="strong">
                                        {{ $log->student?->fullname ?? '—' }}
                                    </flux:table.cell>
                                    <flux:table.cell class="text-sm">{{ $log->destination ?? '—' }}</flux:table.cell>
                                    <flux:table.cell class="text-sm max-w-xs truncate" title="{{ $log->message }}">
                                        {{ Str::limit($log->message, 50) }}
                                    </flux:table.cell>
                                    <flux:table.cell align="center">
                                        @if ($log->status === 'sent')
                                            <span class="inline-flex items-center gap-1 rounded-full bg-green-100 dark:bg-green-900/30 px-2.5 py-0.5 text-xs font-medium text-green-700 dark:text-green-400">
                                                ✓ {{ __('Imetumwa') }}
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 rounded-full bg-red-100 dark:bg-red-900/30 px-2.5 py-0.5 text-xs font-medium text-red-700 dark:text-red-400">
                                                ✗ {{ __('Imeshindwa') }}
                                            </span>
                                        @endif
                                    </flux:table.cell>
                                    <flux:table.cell class="text-xs text-zinc-500 dark:text-zinc-400 max-w-[12rem] truncate" title="{{ $log->response_detail }}">
                                        {{ $log->response_detail ? Str::limit($log->response_detail, 40) : '—' }}
                                    </flux:table.cell>
                                </flux:table.row>
                            @endforeach
                        </flux:table.rows>
                    </flux:table>
                </div>
                <div class="px-4 py-3 border-t border-zinc-200 dark:border-zinc-700">
                    {{ $logs->links() }}
                </div>
            </div>
        @endif
    </div>
@endsection
