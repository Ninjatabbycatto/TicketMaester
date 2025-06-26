<?php

namespace App\Filament\Pages;


use Filament\Pages\Page;
use Filament\Widgets\StatsOverviewWidget\Card;
use Filament\Widgets\StatsOverviewWidget;
use App\Models\Ticket;
use Illuminate\Support\Facades\DB;
use App\Models\Clinic;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;

use Filament\Widgets\HasWidgets;

class TicketStats extends Page implements HasForms
{
    use InteractsWithForms;


    public ?int $clinic_id = null;
    public array $clinics = [];

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static string $view = 'filament.pages.ticket-stats';
    protected static ?string $title = 'Ticket Statistics';

    public ?string $timeframe = '24_hours'; // default timeframe
    public ?string $custom_start_date = null;
    public ?string $custom_end_date = null;
    public string $statistic = 'tickets_by_status';

    


    public function mount(): void
    {
        $this->clinics = Clinic::pluck('name', 'id')->toArray();
        $this->form->fill();
    }

    protected function isClient(): bool {
        return auth()->check() && auth()->user()->user_type === 'client';
    }

    protected function getFormSchema(): array {
        $isClient = $this->isClient();

        // If client, limit clinics to only user's clinic
        $clinicOptions = $isClient
            ? [auth()->user()->clinic_id => Clinic::find(auth()->user()->clinic_id)?->name ?? 'Your Clinic']
            : ['' => 'All Clinics'] + $this->clinics;

        return [
            Grid::make(2)->schema([
                Select::make('clinic_id')
                    ->label('Select Clinic')
                    ->options($clinicOptions)
                    ->nullable(!$isClient)
                    ->default($isClient ? auth()->user()->clinic_id : null)
                    ->disabled($isClient)
                    ->reactive()
                    ->afterStateUpdated(fn ($state) => $this->clinic_id = $state),

                Select::make('timeframe')
                    ->label('Select Timeframe')
                    ->options([
                        '6_hours' => 'Last 6 Hours',
                        '24_hours' => 'Last 24 Hours',
                        '3_days' => 'Last 3 Days',
                        '1_week' => 'Last 1 Week',
                        '1_month' => 'Last 1 Month',
                        'custom' => 'Custom Range',
                    ])
                    ->default('24_hours')
                    ->reactive()
                    ->afterStateUpdated(fn ($state) => $this->timeframe = $state),

                DatePicker::make('custom_start_date')
                    ->label('Start Date')
                    ->visible(fn (callable $get) => $get('timeframe') === 'custom')
                    ->reactive()
                    ->afterStateUpdated(fn ($state) => $this->custom_start_date = $state),

                DatePicker::make('custom_end_date')
                    ->label('End Date')
                    ->visible(fn (callable $get) => $get('timeframe') === 'custom')
                    ->reactive()
                    ->afterStateUpdated(fn ($state) => $this->custom_end_date = $state),
            ]),
        ];

    }

    public function updatedClinicId($value) {
        $this->clinic_id = $value;
    }

    public function updated($propertyName): void {
        if ($propertyName === 'clinic_id') {
            $this->form->fill();
        }
    }

    public function getCards(): array {
        return [
            Card::make('Total Tickets', $this->countTickets()),
            Card::make('New Tickets', $this->countTickets('new')),
            Card::make('In Progress', $this->countTickets('in_progress')),
            Card::make('Acknowledged', $this->countTickets('acknowledged')),
            Card::make('Completed', $this->countTickets('completed')),
            Card::make('Average Time in Backlog (hrs)', $this->averageTimeInBacklog()),
            Card::make('Average Time Before Acknowledged (hrs)', $this->averageTimeBeforeAcknowledged()),

        ];
    }

    protected function countTickets(?string $status = null): int {
        [$start, $end] = $this->getDateRange();

        $query = Ticket::query();

        if ($this->clinic_id) {
            $query->where('clinic_id', $this->clinic_id);
        }

        if ($status) {
            $query->where('status', $status);
        }

        if ($start && $end) {
            $query->whereBetween('created_at', [$start, $end]);
        }

        return $query->count();
    }


    protected function averageTimeBeforeAcknowledged(): string
    {
        [$start, $end] = $this->getDateRange();

        $query = Ticket::query();

        if ($this->clinic_id) {
            $query->where('clinic_id', $this->clinic_id);
        }

        if ($start && $end) {
            $query->whereBetween('created_at', [$start, $end]);
        }

        $avgSeconds = $query
            ->whereNotNull('acknowledged_time')
            ->whereNotNull('created_at')
            ->whereColumn('acknowledged_time', '>=', 'created_at') // avoid negative diffs
            ->avg(DB::raw('TIMESTAMPDIFF(SECOND, created_at, acknowledged_time)'));

        if (is_null($avgSeconds)) {
            return 'N/A';
        }

        return round($avgSeconds / 3600, 2);

    }

    protected function averageTimeInBacklog(): string {
        [$start, $end] = $this->getDateRange();

        $query = Ticket::query();

        if ($this->clinic_id) {
            $query->where('clinic_id', $this->clinic_id);
        }

        if ($start && $end) {
            $query->whereBetween('created_at', [$start, $end]);
        }

        $avgSeconds = $query
            ->whereNotNull('backlog_time')
            ->whereNotNull('inprogress_time')
            ->avg(DB::raw('TIMESTAMPDIFF(SECOND, backlog_time, inprogress_time)'));

        if (is_null($avgSeconds)) {
            return 'N/A';
    }

    return round($avgSeconds / 3600, 2);
    }

    protected function getDateRange(): array {
        $end = Carbon::now();
        switch ($this->timeframe) {
            case '6_hours':
                $start = $end->copy()->subHours(6);
                break;
            case '24_hours':
                $start = $end->copy()->subDay();
                break;
            case '3_days':
                $start = $end->copy()->subDays(3);
                break;
            case '1_week':
                $start = $end->copy()->subWeek();
                break;
            case '1_month':
                $start = $end->copy()->subMonth();
                break;
            case 'custom':
                $start = $this->custom_start_date ? Carbon::parse($this->custom_start_date) : null;
                $end = $this->custom_end_date ? Carbon::parse($this->custom_end_date) : null;
                break;
            default:
                $start = null;
        }

        return [$start, $end];
    }


    public function getClinicsWithBacklogs()
    {
        $backlogCounts = Ticket::select('clinic_id', DB::raw('count(*) as backlog_tickets_count'))
        ->where('status', 'backlogs')
        ->groupBy('clinic_id')
        ->pluck('backlog_tickets_count', 'clinic_id');

        // Get clinics with backlog counts (or zero if none)
        $clinics = Clinic::all()->map(function ($clinic) use ($backlogCounts) {
            $clinic->backlog_tickets_count = $backlogCounts->get($clinic->id, 0);
            return $clinic;
        });

        return $clinics;
    }




}

