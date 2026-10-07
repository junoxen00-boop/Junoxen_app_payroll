<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_payroll_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('employment_type')->nullable();
            $table->string('payroll_status')->default('Active');
            $table->string('pay_frequency')->default('Monthly');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->decimal('default_basic_salary', 12, 2)->default(0);
            $table->decimal('default_bonus', 12, 2)->default(0);
            $table->text('payroll_notes')->nullable();
            $table->text('account_holder_name')->nullable();
            $table->text('bank_name')->nullable();
            $table->text('account_number')->nullable();
            $table->text('routing_identifier')->nullable();
            $table->text('bank_branch')->nullable();
            $table->timestamps();
        });

        Schema::create('employee_leaves', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->string('leave_type', 80);
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('leave_days', 5, 2);
            $table->boolean('is_paid')->default(true);
            $table->string('status', 30)->default('Pending');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['employee_id', 'start_date']);
        });

        Schema::create('employee_timesheets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->date('work_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->unsignedInteger('break_minutes')->default(0);
            $table->decimal('hours_worked', 6, 2)->default(0);
            $table->string('status', 30)->default('Draft');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->unique(['employee_id', 'work_date']);
        });

        Schema::create('employee_superannuations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('fund_name')->nullable();
            $table->text('member_number')->nullable();
            $table->text('usi')->nullable();
            $table->decimal('employee_contribution', 12, 2)->default(0);
            $table->decimal('employer_contribution', 12, 2)->default(0);
            $table->string('status', 30)->default('Inactive');
            $table->timestamps();
        });

        Schema::create('stp_batches', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('payroll_month');
            $table->unsignedSmallInteger('payroll_year');
            $table->unsignedInteger('employee_count')->default(0);
            $table->decimal('gross_payments', 14, 2)->default(0);
            $table->decimal('tax_amount', 14, 2)->default(0);
            $table->string('status', 30)->default('Draft');
            $table->timestamp('submitted_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->unique(['payroll_month', 'payroll_year']);
        });

        Schema::create('payroll_management_settings', function (Blueprint $table) {
            $table->id();
            $table->string('company_name')->default('Junoxen PVT LTD');
            $table->string('pay_frequency')->default('Monthly');
            $table->string('currency', 10)->default('INR');
            $table->string('default_payment_method')->default('Bank Transfer');
            $table->string('lop_calculation_basis')->default('Calendar Days');
            $table->unsignedSmallInteger('payroll_year')->nullable();
            $table->text('payslip_footer')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payroll_management_settings');
        Schema::dropIfExists('stp_batches');
        Schema::dropIfExists('employee_superannuations');
        Schema::dropIfExists('employee_timesheets');
        Schema::dropIfExists('employee_leaves');
        Schema::dropIfExists('employee_payroll_profiles');
    }
};
