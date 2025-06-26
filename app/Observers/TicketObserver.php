<?php

namespace App\Observers;

use App\Models\Ticket;
use App\Models\UserActivityAudit;
use App\Models\Ticket_History;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
class TicketObserver
{
    /**
     * Handle the Ticket "created" event.
     */
    public function created(Ticket $ticket): void
    {
        UserActivityAudit::create([
            'user_id' => Auth::id(),
            'action' => 'created_ticket',
            'target_id' => $ticket->id,
            'target_type' => Ticket::class,
        ]);
    }

    /**
     * Handle the Ticket "updated" event.
     */
    public function updated(Ticket $ticket): void
    {
        $changes = $ticket->getChanges();

        // Skip if no changes
        if (empty($changes)) {
            return;
        }

        if ($ticket->isDirty('status')) {
            $oldStatus = $ticket->getOriginal('status');
            $newStatus = $ticket->status;

            Ticket_History::create([
                'ticket_id' => $ticket->id,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'changed_by' => Auth::id(),
                'changed_at' => Carbon::now(),
            ]);
        }

        UserActivityAudit::create([
            'user_id' => Auth::id(),
            'action' => 'updated_ticket',
            'target_id' => $ticket->id,
            'target_type' => Ticket::class,
        ]);

        

    }

    public function updating(Ticket $ticket)
    {
        if ($ticket->isDirty('status')) {
            $newStatus = $ticket->status->value ?? $ticket->status;
            $now = Carbon::now();
            
            switch ($newStatus) {
                case 'new':
                case 'backlogs':
                    if (is_null($ticket->backlog_time)) {
                        $ticket->backlog_time = $now;
                    }
                    break;

                case 'in_progress':
                    if (is_null($ticket->inprogress_time)) {
                        $ticket->inprogress_time = $now;
                    }
                    break;

                case 'acknowledged':
                    if (is_null($ticket->acknowledged_time)) {
                        $ticket->acknowledged_time = $now;
                    }
                    break;

                // Add other statuses if needed
            }
        }
    }

    /**
     * Handle the Ticket "deleted" event.
     */
    public function deleted(Ticket $ticket): void
    {
        UserActivityAudit::create([
            'user_id' => Auth::id(),
            'action' => 'deleted_ticket',
            'target_id' => $ticket->id,
            'target_type' => Ticket::class,
        ]);
    }


}
