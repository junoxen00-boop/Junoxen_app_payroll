<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PayrollAuditLog extends Model
{
    public $timestamps = false;
    const UPDATED_AT = null;

    protected $fillable = ['payroll_id', 'user_id', 'action', 'old_values', 'new_values', 'ip_address', 'created_at'];

    protected function casts(): array
    {
        return ['old_values' => 'array', 'new_values' => 'array', 'created_at' => 'datetime'];
    }

    public function payroll(): BelongsTo { return $this->belongsTo(Payroll::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
}
