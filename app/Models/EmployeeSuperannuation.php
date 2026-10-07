<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeSuperannuation extends Model
{
    protected $fillable = [
        'employee_id',
        'fund_name',
        'member_number',
        'usi',
        'employee_contribution',
        'employer_contribution',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'member_number' => 'encrypted',
            'usi' => 'encrypted',
            'employee_contribution' => 'decimal:2',
            'employer_contribution' => 'decimal:2',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
