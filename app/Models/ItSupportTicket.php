<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ItSupportTicket extends Model
{
    /*
    |--------------------------------------------------------------------------
    | Ticket Categories
    |--------------------------------------------------------------------------
    */

    public const CATEGORIES = [
        'Hardware',
        'Software',
        'Email',
        'Network / Internet',
        'Login / Password',
        'Printer',
        'Access / Permission',
        'Application Issue',
        'Security',
        'Other',
    ];


    /*
    |--------------------------------------------------------------------------
    | Ticket Priorities
    |--------------------------------------------------------------------------
    */

    public const PRIORITIES = [
        'Low',
        'Medium',
        'High',
        'Critical',
    ];


    /*
    |--------------------------------------------------------------------------
    | Ticket Statuses
    |--------------------------------------------------------------------------
    */

    public const STATUSES = [
        'Open',
        'Assigned',
        'In Progress',
        'Resolved',
        'Closed',
        'Reopened',
    ];


    /*
    |--------------------------------------------------------------------------
    | Mass Assignable Fields
    |--------------------------------------------------------------------------
    */

    protected $fillable = [
        'ticket_number',
        'employee_id',
        'department_id',
        'subject',
        'category',
        'description',
        'priority',
        'status',
        'assigned_to',
        'resolution',
        'resolved_by',
        'resolved_at',
    ];


    /*
    |--------------------------------------------------------------------------
    | Casts
    |--------------------------------------------------------------------------
    */

    protected function casts(): array
    {
        return [
            'resolved_at' => 'datetime',
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function employee(): BelongsTo
    {
        return $this->belongsTo(
            Employee::class,
            'employee_id'
        );
    }


    public function department(): BelongsTo
    {
        return $this->belongsTo(
            Department::class,
            'department_id'
        );
    }


    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(
            Employee::class,
            'assigned_to'
        );
    }


    public function resolver(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'resolved_by'
        );
    }


    public function comments(): HasMany
    {
        return $this->hasMany(
            ItSupportTicketComment::class,
            'ticket_id'
        )->orderBy('created_at');
    }
}