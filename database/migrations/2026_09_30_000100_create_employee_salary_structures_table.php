<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('employee_salary_structures')) {
            return;
        }

        Schema::create('employee_salary_structures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnUpdate()->cascadeOnDelete();
            $table->decimal('basic_salary', 12, 2)->default(0);
            $table->decimal('hra', 12, 2)->default(0);
            $table->decimal('conveyance_allowance', 12, 2)->default(0);
            $table->decimal('medical_allowance', 12, 2)->default(0);
            $table->decimal('special_allowance', 12, 2)->default(0);
            $table->decimal('other_allowance', 12, 2)->default(0);
            $table->decimal('overtime_rate', 12, 2)->default(0);
            $table->decimal('pf_deduction', 12, 2)->default(0);
            $table->decimal('esi_deduction', 12, 2)->default(0);
            $table->decimal('professional_tax', 12, 2)->default(0);
            $table->decimal('tds', 12, 2)->default(0);
            $table->decimal('loan_deduction', 12, 2)->default(0);
            $table->decimal('other_deduction', 12, 2)->default(0);
            $table->date('effective_from');
            $table->date('effective_to')->nullable();
            $table->enum('status', ['Active', 'Inactive'])->default('Active');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['employee_id', 'status', 'effective_from'], 'ess_emp_status_eff_idx');
            $table->index(['effective_from', 'effective_to'], 'ess_effective_range_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_salary_structures');
    }
};
