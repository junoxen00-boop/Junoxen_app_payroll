<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StpBatch extends Model
{
    protected $fillable = [
        'payroll_month',
        'payroll_year',
        'employee_count',
        'gross_payments',
        'tax_amount',
        'status',
        'submitted_at',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'gross_payments' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'submitted_at' => 'datetime',
        ];
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
