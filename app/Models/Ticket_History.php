<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Ticket_History extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id', 'old_status', 'new_status', 'changed_by', 'changed_at'
    ];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    public function changer()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

}
