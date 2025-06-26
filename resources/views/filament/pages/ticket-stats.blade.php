<x-filament::page>
    <div class="mb-6 border-b border-gray-200 pb-6">
        <form wire:submit.prevent="save" class="grid grid-cols-2 gap-4">
            {{ $this->form }}
        </form>
    </div>

    <div class="mb-6 border-b border-gray-200 pb-6 grid grid-cols-1 md:grid-cols-3 gap-6">
        @foreach ($this->getCards() as $card)
            <x-filament::card>
                <div class="text-sm font-medium text-gray-500">{{ $card->getLabel() }}</div>
                <div class="mt-2 text-3xl font-semibold text-gray-900">
                    {{ $card->getDescription() ?? $card->getValue() }}
                </div>
            </x-filament::card>
        @endforeach
    </div>

    <div class="mb-6">
        <form wire:submit.prevent="save" class="mb-4">
            {{ $this->form }}
        </form>
        @livewire(\App\Filament\Widgets\TicketStatusChart::class, [
            'clinic_id' => $clinic_id,
            'timeframe' => $timeframe,
            'custom_start_date' => $custom_start_date,
            'custom_end_date' => $custom_end_date,
            'statistic' => 'tickets_by_status', // or bind this to a page property if you want
        ])
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 border border-gray-300 rounded-md">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-gray-300">
                        Clinic Name
                    </th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-gray-300">
                        Backlog Tickets
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach ($this->getClinicsWithBacklogs() as $clinic)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-gray-900">
                            {{ $clinic->name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-gray-900 font-semibold">
                            {{ $clinic->backlog_tickets_count }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-filament::page>
