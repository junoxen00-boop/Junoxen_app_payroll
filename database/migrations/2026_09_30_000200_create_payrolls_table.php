<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('payrolls')) {
            return;
        }

        Schema::create('payrolls', function (Blueprint $table) {
            $table->id();
            $table->string('payroll_number', 40)->unique('payroll_number_uq');
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnUpdate()->restrictOnDelete();
            $table->unsignedTinyInteger('payroll_month');
            $table->unsignedSmallInteger('payroll_year');
            $table->date('period_start');
            $table->date('period_end');

            $table->decimal('basic_salary', 12, 2)->default(0);
            $table->decimal('hra', 12, 2)->default(0);
            $table->decimal('conveyance_allowance', 12, 2)->default(0);
            $table->decimal('medical_allowance', 12, 2)->default(0);
            $table->decimal('special_allowance', 12, 2)->default(0);
            $table->decimal('other_allowance', 12, 2)->default(0);
            $table->decimal('overtime_hours', 8, 2)->default(0);
            $table->decimal('overtime_amount', 12, 2)->default(0);
            $table->decimal('bonus', 12, 2)->default(0);
            $table->decimal('incentive', 12, 2)->default(0);
            $table->decimal('reimbursement', 12, 2)->default(0);
            $table->decimal('other_earnings', 12, 2)->default(0);
            $table->decimal('gross_salary', 12, 2)->default(0);

            $table->decimal('pf_deduction', 12, 2)->default(0);
            $table->decimal('esi_deduction', 12, 2)->default(0);
            $table->decimal('professional_tax', 12, 2)->default(0);
            $table->decimal('tds', 12, 2)->default(0);
            $table->decimal('loan_deduction', 12, 2)->default(0);
            $table->decimal('leave_deduction', 12, 2)->default(0);
            $table->decimal('other_deduction', 12, 2)->default(0);
            $table->decimal('total_deductions', 12, 2)->default(0);
            $table->decimal('net_salary', 12, 2)->default(0);

            $table->enum('status', ['Draft', 'Generated', 'Paid', 'Cancelled'])->default('Generated');
            $table->timestamp('generated_at')->nullable();
            $table->foreignId('generated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->date('payment_date')->nullable();
            $table->string('payment_method', 50)->nullable();
            $table->string('payment_reference', 120)->nullable();
            $table->text('payment_notes')->nullable();
            $table->foreignId('paid_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['employee_id', 'payroll_month', 'payroll_year'], 'payroll_emp_month_year_uq');
            $table->index(['payroll_year', 'payroll_month', 'status'], 'payroll_period_status_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payrolls');
    }
};
