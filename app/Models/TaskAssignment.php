<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskAssignment extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'Pending';
    public const STATUS_IN_PROGRESS = 'In Progress';
    public const STATUS_COMPLETED = 'Completed';

    public const STATUSES = [
        self::STATUS_PENDING,
        self::STATUS_IN_PROGRESS,
        self::STATUS_COMPLETED,
    ];

    protected $fillable = [
        'task_id',
        'employee_id',
        'status',
        'progress',
        'remarks',
        'manager_advice',
        'attachment',
        'review_status',
        'review_comment',
        'reviewed_by',
        'reviewed_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'progress' => 'integer',
            'completed_at' => 'datetime',
            'reviewed_at' => 'datetime',
        ];
    }

    /**
     * Task this assignment belongs to.
     */
    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    /**
     * Employee assigned to this task.
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Manager/Admin who reviewed the task.
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}