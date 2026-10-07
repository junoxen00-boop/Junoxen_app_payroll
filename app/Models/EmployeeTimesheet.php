<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeTimesheet extends Model
{
    protected $fillable = [
        'employee_id',
        'work_date',
        'start_time',
        'end_time',
        'break_minutes',
        'hours_worked',
        'status',
        'notes',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'work_date' => 'date',
            'hours_worked' => 'decimal:2',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
