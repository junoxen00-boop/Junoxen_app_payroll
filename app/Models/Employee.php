<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Employee extends Model
{
    protected $fillable = [
        'user_id',
        'employee_id',
        'full_name',
        'email',
        'mobile_number',
        'department_id',
        'designation',
        'joining_date',
        'status',
    ];

    protected $casts = [
        'joining_date' => 'date',
    ];


    /**
     * Employee login account.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }


    /**
     * Employee belongs to a department.
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }


    /**
     * Employee has many task assignments.
     */
    public function taskAssignments(): HasMany
    {
        return $this->hasMany(
            TaskAssignment::class,
            'employee_id'
        );
    }


    /**
     * Employee salary structures.
     */
    public function salaryStructures(): HasMany
    {
        return $this->hasMany(
            EmployeeSalaryStructure::class,
            'employee_id'
        );
    }


    /**
     * Employee payroll records.
     */
    public function payrolls(): HasMany
    {
        return $this->hasMany(
            Payroll::class,
            'employee_id'
        );
    }


    /**
     * Payroll-management profile.
     */
    public function payrollProfile(): HasOne
    {
        return $this->hasOne(
            EmployeePayrollProfile::class,
            'employee_id'
        );
    }


    /**
     * Employee leave records.
     */
    public function leaves(): HasMany
    {
        return $this->hasMany(
            EmployeeLeave::class,
            'employee_id'
        );
    }


    /**
     * Employee timesheet records.
     */
    public function timesheets(): HasMany
    {
        return $this->hasMany(
            EmployeeTimesheet::class,
            'employee_id'
        );
    }


    /**
     * Employee superannuation details.
     */
    public function superannuation(): HasOne
    {
        return $this->hasOne(
            EmployeeSuperannuation::class,
            'employee_id'
        );
    }


    /**
     * IT support tickets raised by this employee.
     */
    public function itSupportTickets(): HasMany
    {
        return $this->hasMany(
            ItSupportTicket::class,
            'employee_id'
        );
    }


    /**
     * IT support tickets assigned to this employee.
     */
    public function assignedItSupportTickets(): HasMany
    {
        return $this->hasMany(
            ItSupportTicket::class,
            'assigned_to'
        );
    }


    /**
     * Employee belongs to many tasks
     * through task_assignments.
     */
    public function tasks(): BelongsToMany
    {
        return $this->belongsToMany(
            Task::class,
            'task_assignments',
            'employee_id',
            'task_id'
        )
            ->withPivot([
                'status',
                'progress',
                'remarks',
                'attachment',
                'review_status',
                'review_comment',
                'reviewed_by',
                'reviewed_at',
                'completed_at',
            ])
            ->withTimestamps();
    }
}