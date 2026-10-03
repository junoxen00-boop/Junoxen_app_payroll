<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Task extends Model
{
    use HasFactory;

    public const PRIORITY_LOW = 'Low';
    public const PRIORITY_MEDIUM = 'Medium';
    public const PRIORITY_HIGH = 'High';
    public const PRIORITY_CRITICAL = 'Critical';

    public const PRIORITIES = [
        self::PRIORITY_LOW,
        self::PRIORITY_MEDIUM,
        self::PRIORITY_HIGH,
        self::PRIORITY_CRITICAL,
    ];

   protected $fillable = [
    'task_code',
    'title',
    'description',

    'week',
    'category',
    'month',

    'priority',
    'start_date',
    'due_date',
    'created_by',
];

    protected static function booted(): void
    {
        static::creating(function (Task $task): void {
            if (blank($task->task_code)) {
                $task->task_code = 'TSK-' . strtoupper(Str::random(8));
            }
        });
    }

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'due_date' => 'date',
        ];
    }

    /**
     * User who created the task.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Task assignments.
     */
    public function assignments(): HasMany
    {
        return $this->hasMany(TaskAssignment::class);
    }

    /**
     * Assigned employees.
     */
    public function employees(): BelongsToMany
    {
        return $this->belongsToMany(
            Employee::class,
            'task_assignments',
            'task_id',
            'employee_id'
        )->withPivot([
            'status',
            'progress',
            'remarks',
            'completed_at',
        ])->withTimestamps();
    }
}