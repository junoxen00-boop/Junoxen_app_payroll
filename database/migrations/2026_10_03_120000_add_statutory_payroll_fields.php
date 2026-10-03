<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employee_salary_structures', function (Blueprint $table) {
            if (! Schema::hasColumn('employee_salary_structures', 'pf_applicable')) {
                $table->boolean('pf_applicable')->default(true)->after('overtime_rate');
            }

            if (! Schema::hasColumn('employee_salary_structures', 'pf_wage_basis')) {
                $table->decimal('pf_wage_basis', 12, 2)->nullable()->after('pf_applicable');
            }
        });

        Schema::table('payrolls', function (Blueprint $table) {
            if (! Schema::hasColumn('payrolls', 'leave_days')) {
                $table->decimal('leave_days', 5, 2)->default(0)->after('leave_deduction');
            }

            if (! Schema::hasColumn('payrolls', 'lop_deduction')) {
                $table->decimal('lop_deduction', 12, 2)->default(0)->after('lop_days');
            }

            if (! Schema::hasColumn('payrolls', 'paid_days')) {
                $table->decimal('paid_days', 5, 2)->default(0)->after('lop_deduction');
            }
        });
    }

    public function down(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            $columns = [];
            foreach (['leave_days', 'lop_deduction', 'paid_days'] as $column) {
                if (Schema::hasColumn('payrolls', $column)) {
                    $columns[] = $column;
                }
            }
            if ($columns) {
                $table->dropColumn($columns);
            }
        });

        Schema::table('employee_salary_structures', function (Blueprint $table) {
            $columns = [];
            foreach (['pf_applicable', 'pf_wage_basis'] as $column) {
                if (Schema::hasColumn('employee_salary_structures', $column)) {
                    $columns[] = $column;
                }
            }
            if ($columns) {
                $table->dropColumn($columns);
            }
        });
    }
};
