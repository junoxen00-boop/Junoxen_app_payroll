<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('payroll_payments')) {
            return;
        }

        Schema::create('payroll_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payroll_id')->constrained('payrolls')->cascadeOnDelete();
            $table->decimal('amount', 12, 2);
            $table->date('payment_date');
            $table->string('payment_method', 50);
            $table->string('transaction_reference', 120)->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['payroll_id', 'payment_date'], 'payroll_payment_date_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payroll_payments');
    }
};
