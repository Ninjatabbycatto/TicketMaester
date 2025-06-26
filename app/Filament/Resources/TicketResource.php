<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TicketResource\Pages;
use App\Filament\Resources\TicketResource\RelationManagers;
use App\Models\Ticket;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\EditAction;
use Illuminate\Support\Facades\Auth;



class TicketResource extends Resource
{
    protected static ?string $model = Ticket::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form->schema([
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
                ->required(),
            Select::make('priority')
                ->label('Priority')
                ->options([
                    'low' => 'Low',
                    'normal' => 'Normal',
                    'high' => 'High',
                ])
                ->default('normal')
                ->required(),

        ]);
    }

    public static function getEloquentQuery(): Builder {
        $query = parent::getEloquentQuery();

        $user = Auth::user();

        if ($user->user_type === 'client') {
            // Restrict tickets to only those matching the client's clinic_id
            $query->where('clinic_id', $user->clinic_id);
        }

        return $query;
    }


    public function canDrag(): bool {
        // Only allow drag for non-clients
        return Auth::user()->user_type !== 'client';
    }


    

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable(),
                TextColumn::make('title')->searchable()->sortable(),
                TextColumn::make('status')->sortable(),
                TextColumn::make('priority')->sortable(),
                TextColumn::make('created_at')->dateTime()->sortable(),
                TextColumn::make('clinic.name')
                    ->label('Clinic')
                    ->sortable()
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }




    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTickets::route('/'),
            'create' => Pages\CreateTicket::route('/create'),
            'edit' => Pages\EditTicket::route('/{record}/edit'),
        ];
    }
}
