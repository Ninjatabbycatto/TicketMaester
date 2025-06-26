<?php

namespace App\Livewire;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Ticket;
use Illuminate\Support\Facades\DB;

class TicketStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Tickets')
                ->description(Ticket::count()),

            Stat::make('New Tickets')
                ->description(Ticket::where('status', 'new')->count()),

            Stat::make('In Progress')
                ->description(Ticket::where('status', 'in_progress')->count()),

            Stat::make('Acknowledged')
                ->description(Ticket::where('status', 'acknowledged')->count()),

            Stat::make('Completed')
                ->description(Ticket::where('status', 'completed')->count()),

            Stat::make('Average Time in Backlog (hrs)')
                ->description($this->averageTimeInBacklog()),
        ];
    }

    protected function averageTimeInBacklog(): string
    {
        $avgSeconds = Ticket::whereNotNull('backlog_time')
            ->whereNotNull('inprogress_time')
            ->avg(DB::raw('TIMESTAMPDIFF(SECOND, backlog_time, inprogress_time)'));

        if (is_null($avgSeconds)) {
            return 'N/A';
        }

        return round($avgSeconds / 3600, 2);
    }
}