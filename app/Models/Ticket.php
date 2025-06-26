<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use App\Enums\TicketStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;


class Ticket extends Model implements Sortable
{
    // Allow mass assignment on these fields
    use HasFactory;
    use SortableTrait;

    protected $fillable = [
        'title', 
        'description', 
        'clinic_id', 
        'created_by',
        'backlog_time', 
        'inprogress_time', 
        'acknowledged_time', 
        'order_column',
        'status',
        'taken_by'
        
    ];

    public function takenBy()
{
    return $this->belongsTo(User::class, 'taken_by');
}

    public function clinic()
    {
        return $this->belongsTo(Clinic::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Cast the status attribute to the TicketStatus enum
    protected $casts = [
        'status' => TicketStatus::class,
    ];

    public function createdBy() {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getTimeInBacklogAttribute() {
        if ($this->backlog_time) {
            $endTime = $this->inprogress_time ?? $this->acknowledged_time ?? now();
            return $endTime->diffInSeconds($this->backlog_time);
        }
        return 0;
    }

    public function getTimeInProgressAttribute() {
        if ($this->inprogress_time) {
            $endTime = $this->acknowledged_time ?? now();
            return $endTime->diffInSeconds($this->inprogress_time);
        }
        return 0;
    }

    public function getTimeAcknowledgedAttribute() {
        if ($this->acknowledged_time) {
            return now()->diffInSeconds($this->acknowledged_time);
        }
        return 0;
    }

    
}