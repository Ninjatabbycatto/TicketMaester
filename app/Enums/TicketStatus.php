<?php

namespace App\Enums;
use Illuminate\Support\Collection;

//use Mokhosh\FilamentKanban\IsKanbanStatus;

enum TicketStatus: string
{
    case New = 'new';
    case Backlogs = 'backlogs';
    case InProgress = 'in_progress';
    case Acknowledged = 'acknowledged';
    case Completed = 'completed';

    public static function statuses(): Collection
    {
        return collect([
            ['id' => self::New->value, 'title' => 'New'],
            ['id' => self::Acknowledged->value, 'title' => 'Acknowledged'],
            ['id' => self::InProgress->value, 'title' => 'In Progress'],
            ['id' => self::Completed->value, 'title' => 'Completed'],
            ['id' => self::Backlogs->value, 'title' => 'Backlogs'],
        ]);
    }

    

}
