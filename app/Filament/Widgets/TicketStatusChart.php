<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Ticket;
use Illuminate\Support\Facades\DB;
use Filament\Forms\Components\Select;


class TicketStatusChart extends ChartWidget
{
    protected static ?string $heading = 'Tickets by Status';

    public ?int $clinic_id = null;
    public string $timeframe = '24_hours';
    public ?string $custom_start_date = null;
    public ?string $custom_end_date = null;
    public string $statistic = 'tickets_by_status';


    protected function getType(): string
    {
        return 'bar';
    }

    protected function getFormSchema(): array
    {
        return [
            Select::make('statistic')
                ->label('Select Statistic')
                ->options([
                    'tickets_by_status' => 'Tickets by Status',
                    'tickets_by_clinic' => 'Tickets by Clinic',
                    'tickets_by_priority' => 'Tickets by Priority',
                    'average_time_in_backlog' => 'Average Time in Backlog',
                    'average_time_before_ack' => 'Average Time Before Acknowledged',
                    'tickets_over_time' => 'Tickets Created Over Time',
                ])
                ->reactive()
                ->afterStateUpdated(fn ($state) => $this->statistic = $state),
        ];
    }


    public function getData(): array
    {
        switch ($this->statistic) {
            case 'tickets_by_status':
                return $this->getTicketsByStatusData();
            case 'tickets_by_clinic':
                return $this->getTicketsByClinicData();
            case 'tickets_by_priority':
                return $this->getTicketsByPriorityData();
            case 'average_time_in_backlog':
                return $this->getAverageTimeInBacklogData();
            case 'average_time_before_ack':
                return $this->getAverageTimeBeforeAckData();
            case 'tickets_over_time':
                return $this->getTicketsOverTimeData();
            default:
                return ['labels' => [], 'datasets' => []];
        }
    }

    protected function getTicketsByStatusData(): array {
        $data = Ticket::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        return [
            'labels' => array_keys($data),
            'datasets' => [[
                'label' => 'Tickets',
                'data' => array_values($data),
                'backgroundColor' => 'rgba(59, 130, 246, 0.7)',
            ]],
        ];
    }

    protected function getTicketsByClinicData(): array {
        $data = Ticket::join('clinics', 'tickets.clinic_id', '=', 'clinics.id')
            ->select('clinics.name', DB::raw('count(*) as total'))
            ->groupBy('clinics.name')
            ->pluck('total', 'name')
            ->toArray();

        return [
            'labels' => array_keys($data),
            'datasets' => [[
                'label' => 'Tickets',
                'data' => array_values($data),
                'backgroundColor' => 'rgba(16, 185, 129, 0.7)', // Tailwind green-500
            ]],
        ];
    }

    protected function getAverageTimeInBacklogData(): array {
        $avgSeconds = Ticket::whereNotNull('backlog_time')
            ->whereNotNull('inprogress_time')
            ->avg(DB::raw('TIMESTAMPDIFF(SECOND, backlog_time, inprogress_time)'));

        $hours = $avgSeconds ? round($avgSeconds / 3600, 2) : 0;

        return [
            'labels' => ['Average Time in Backlog (hrs)'],
            'datasets' => [[
                'label' => 'Hours',
                'data' => [$hours],
                'backgroundColor' => 'rgba(234, 179, 8, 0.7)', // Tailwind yellow-400
            ]],
        ];
    }

    public function updatedClinicId($value)
{
    $this->clinic_id = $value;
    $this->emitSelf('refreshChart');
}

    public function updatedTimeframe($value)
    {
        $this->timeframe = $value;
        $this->emitSelf('refreshChart');
    }





    
}
