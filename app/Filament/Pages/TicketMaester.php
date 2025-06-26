<?php

namespace App\Filament\Pages;

use Mokhosh\FilamentKanban\Pages\KanbanBoard;
use App\Models\Ticket;
use App\Enums\TicketStatus;
use Illuminate\Support\Collection;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;

class TicketMaester extends KanbanBoard
{
    protected static string $model = Ticket::class;

    protected static string $statusEnum = TicketStatus::class;

    public function onStatusChanged(string|int $recordId, string $status, array $fromOrderedIds, array $toOrderedIds): void {
        $user = auth()->user();

        if ($user->user_type === 'client') {
            // Prevent clients from changing status
            return;
        }

        Ticket::findOrFail($recordId)->update(['status' => $status]);
        Ticket::setNewOrder($toOrderedIds);
    }

    // Optional: show in Filament navigation
    protected static bool $shouldRegisterNavigation = true;


    protected static ?string $title = "DashLabs TicketMaester";

    protected function getEditModalFormSchema(string|int|null $recordId): array {
        

       return ([
            TextInput::make('title')
                ->required()
                ->maxLength(255),

            Textarea::make('description')
                ->rows(5),
            Select::make('status')
                ->options([
                    'new' => 'New',
                    'in_progress' => 'In Progress',
                    'acknowledged' => 'Acknowledged',
                    'completed' => 'Completed',
                ])
                ->default('new')
                ->required()
                ->disabled(fn () => Auth::user()->user_type === 'client')
                ->default(fn () => Auth::user()->user_type === 'client' ? 'new' : 'new'),
            Select::make('priority')
                ->label('Priority')
                ->options([
                    'low' => 'Low',
                    'normal' => 'Normal',
                    'high' => 'High',
                ])
                ->default('normal')
                ->required()
                ->disabled(fn () => Auth::user()->user_type === 'client')
                ->default(fn () => Auth::user()->user_type === 'client' ? 'new' : 'new'),
        ]);

    }
    

    protected function records(): Collection {
        $user = Auth::user();

        if ($user->user_type === 'client') {
            // Return tickets filtered by user's clinic_id
            return Ticket::where('clinic_id', $user->clinic_id)->get();
        }
        // For admin or staff, return all tickets
        return Ticket::all();
    }

    




    protected function getHeaderActions(): array {
        $isClient = Auth::user()->user_type === 'client';

        return [
            CreateAction::make()
                ->model(Ticket::class)
                ->form([
                    TextInput::make('title')
                        ->required()
                        ->maxLength(255),

                    Textarea::make('description')
                        ->rows(5),

                    // Status select - disabled for clients, default to 'new'
                    Select::make('status')
                        ->options([
                            'new' => 'New',
                            'in_progress' => 'In Progress',
                            'acknowledged' => 'Acknowledged',
                            'completed' => 'Completed',
                        ])
                        ->default('new')
                        ->required()
                        ->disabled($isClient)
                        ->default(fn () => $isClient ? 'new' : 'new'),

                    // Priority select - disabled for clients, default to 'normal'
                    Select::make('priority')
                        ->label('Priority')
                        ->options([
                            'low' => 'Low',
                            'normal' => 'Normal',
                            'high' => 'High',
                        ])
                        ->default('normal')
                        ->required()
                        ->disabled($isClient)
                        ->default(fn () => $isClient ? 'normal' : 'normal'),
                ])
                ->action(function (array $data) {
                    // Force status and priority for clients regardless of form input
                    if (Auth::user()->user_type === 'client') {
                        $data['status'] = 'new';
                        $data['priority'] = 'normal';
                    }

                    $data['created_by'] = auth()->id();
                    $data['clinic_id'] = auth()->user()->clinic_id; // auto-assign user's clinic_id

                    Ticket::create($data);
                })
                ->successNotificationTitle('Ticket created successfully'),
        ];
    }

    

}