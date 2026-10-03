<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('payroll_audit_logs')) {
            return;
        }

        Schema::create('payroll_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payroll_id')->nullable()->constrained('payrolls')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action', 80);
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['payroll_id', 'action'], 'payroll_audit_action_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payroll_audit_logs');
    }
};
