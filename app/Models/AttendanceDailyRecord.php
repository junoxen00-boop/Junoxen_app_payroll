<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceDailyRecord extends Model
{
    protected $fillable = [
        'attendance_import_id',
        'employee_id',
        'source_employee_id',
        'source_employee_name',
        'source_department',
        'attendance_date',
        'raw_punches',
        'first_entry',
        'last_exit',
        'worked_minutes',
        'scheduled_minutes',
        'late_minutes',
        'deductible_minutes',
        'attendance_status',
        'review_status',
        'match_method',
        'review_note',
        'override_reason',
        'modified_by',
        'modified_at',
    ];

    protected function casts(): array
    {
        return [
            'attendance_date' => 'date',
            'modified_at' => 'datetime',
        ];
    }

    public function import(): BelongsTo
    {
        return $this->belongsTo(AttendanceImport::class, 'attendance_import_id');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function modifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'modified_by');
    }
}