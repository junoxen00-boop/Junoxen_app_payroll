<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItSupportTicketComment extends Model
{
    protected $fillable = [
        'ticket_id',
        'user_id',
        'message',
        'type',
    ];

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(
            ItSupportTicket::class,
            'ticket_id'
        );
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'user_id'
        );
    }
}