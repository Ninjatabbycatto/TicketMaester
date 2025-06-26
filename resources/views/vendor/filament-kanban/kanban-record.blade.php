
@php
    $priorityColors = [
        'low' => '#0AF56D',
        'normal' => '#F59F0A',
        'high' => '#940AF5',
    ];

    $borderColor = $priorityColors[$record->priority ?? 'normal'] ?? 'gray';
@endphp


<div
    id="{{ $record->getKey() }}"
    wire:click="recordClicked('{{ $record->getKey() }}', {{ @json_encode($record) }})"
    @style([
        "border-left: 4px solid {$borderColor}"
    ])
    class="relative record bg-white dark:bg-gray-700 rounded-lg px-4 py-2 cursor-grab font-medium text-gray-600 dark:text-gray-200"
    @if($record->timestamps && now()->diffInSeconds($record->{$record::UPDATED_AT}, true) < 3)
        x-data
        x-init="
            $el.classList.add('animate-pulse-twice', 'bg-primary-100', 'dark:bg-primary-800')
            $el.classList.remove('bg-white', 'dark:bg-gray-700')
            setTimeout(() => {
                $el.classList.remove('bg-primary-100', 'dark:bg-primary-800')
                $el.classList.add('bg-white', 'dark:bg-gray-700')
            }, 3000)
        "
    @endif
>
    {{-- Clinic Name at top --}}
    <div class="text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1 truncate max-w-full">
        {{ $record->clinic?->name ?? 'No Clinic' }}
    </div>

    {{-- Ticket Title --}}
    <div class="truncate max-w-full mb-4">
        {{ $record->{static::$recordTitleAttribute} }}
    </div>

    {{-- Footer container --}}
    <div class="flex justify-between items-center border-t pt-2 mt-2">
    {{-- Assigned to (taken_by) on the left --}}
    <div class="flex items-center space-x-2">
        @if($record->takenBy)
            <x-filament::avatar
                :src="$record->takenBy->getFilamentAvatarUrl()"
                alt="{{ $record->takenBy->name }}"
                size="sm"
            />
            
            <span class="text-xs font-medium text-gray-400 dark:text-gray-300 truncate max-w-xs ml-2">
                Assigned to: 
            </span>
            <span class="text-xs font-medium text-gray-700 dark:text-gray-300 truncate max-w-xs ml-2">
                {{ $record->takenBy->name }}
            </span>
        @else
            <span class="text-sm italic text-gray-400 dark:text-gray-500">Unassigned</span>
        @endif
    </div>

    {{-- Created by on the right --}}
    <div class="flex items-center space-x-2">
        @if($record->createdBy)
            <x-filament::avatar
                :src="$record->createdBy->getFilamentAvatarUrl()"
                alt="{{ $record->createdBy->name }}"
                size="sm"
            />
        @else
            <x-filament::avatar
                :src="null"
                alt="User"
                size="sm"
            />
        @endif
    </div>
</div>

</div>
