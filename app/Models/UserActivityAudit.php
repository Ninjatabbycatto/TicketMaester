<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserActivityAudit extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'action', 'target_id', 'target_type'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
