<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /*
        |--------------------------------------------------------------------------
        | EMPLOYEES
        |--------------------------------------------------------------------------
        */

        if (! Schema::hasColumn('employees', 'attendance_employee_id')) {
            Schema::table('employees', function (Blueprint $table) {
                $table->string('attendance_employee_id', 80)
                    ->nullable()
                    ->unique()
                    ->after('employee_id');
            });
        }


        /*
        |--------------------------------------------------------------------------
        | ATTENDANCE IMPORTS
        |--------------------------------------------------------------------------
        */

        if (! Schema::hasColumn('attendance_imports', 'original_filename')) {
            Schema::table('attendance_imports', function (Blueprint $table) {
                $table->string('original_filename')
                    ->nullable()
                    ->after('id');
            });
        }


        if (! Schema::hasColumn('attendance_imports', 'needs_review_count')) {
            Schema::table('attendance_imports', function (Blueprint $table) {
                $table->unsignedInteger('needs_review_count')
                    ->default(0)
                    ->after('unmatched_count');
            });
        }


        if (! Schema::hasColumn('attendance_imports', 'confirmed_at')) {
            Schema::table('attendance_imports', function (Blueprint $table) {
                $table->timestamp('confirmed_at')
                    ->nullable()
                    ->after('imported_at');
            });
        }


        if (! Schema::hasColumn('attendance_imports', 'confirmed_by')) {
            Schema::table('attendance_imports', function (Blueprint $table) {
                $table->foreignId('confirmed_by')
                    ->nullable()
                    ->after('confirmed_at')
                    ->constrained('users')
                    ->nullOnDelete();
            });
        }


        /*
        |--------------------------------------------------------------------------
        | ATTENDANCE DAILY RECORDS
        |--------------------------------------------------------------------------
        */

        if (! Schema::hasColumn(
            'attendance_daily_records',
            'source_employee_id'
        )) {
            Schema::table(
                'attendance_daily_records',
                function (Blueprint $table) {
                    $table->string('source_employee_id', 80)
                        ->nullable()
                        ->after('employee_id');
                }
            );
        }


        if (! Schema::hasColumn(
            'attendance_daily_records',
            'source_employee_name'
        )) {
            Schema::table(
                'attendance_daily_records',
                function (Blueprint $table) {
                    $table->string('source_employee_name')
                        ->nullable()
                        ->after('source_employee_id');
                }
            );
        }


        if (! Schema::hasColumn(
            'attendance_daily_records',
            'source_department'
        )) {
            Schema::table(
                'attendance_daily_records',
                function (Blueprint $table) {
                    $table->string('source_department')
                        ->nullable()
                        ->after('source_employee_name');
                }
            );
        }


        if (! Schema::hasColumn(
            'attendance_daily_records',
            'deductible_minutes'
        )) {
            Schema::table(
                'attendance_daily_records',
                function (Blueprint $table) {
                    $table->unsignedInteger('deductible_minutes')
                        ->default(0)
                        ->after('late_minutes');
                }
            );
        }


        if (! Schema::hasColumn(
            'attendance_daily_records',
            'match_method'
        )) {
            Schema::table(
                'attendance_daily_records',
                function (Blueprint $table) {
                    $table->string('match_method', 40)
                        ->nullable()
                        ->after('review_status');
                }
            );
        }


        if (! Schema::hasColumn(
            'attendance_daily_records',
            'review_note'
        )) {
            Schema::table(
                'attendance_daily_records',
                function (Blueprint $table) {
                    $table->text('review_note')
                        ->nullable()
                        ->after('match_method');
                }
            );
        }


        /*
        |--------------------------------------------------------------------------
        | PAYROLL MANAGEMENT SETTINGS
        |--------------------------------------------------------------------------
        */

        if (! Schema::hasColumn(
            'payroll_management_settings',
            'attendance_payroll_enabled'
        )) {
            Schema::table(
                'payroll_management_settings',
                function (Blueprint $table) {
                    $table->boolean('attendance_payroll_enabled')
                        ->default(false)
                        ->after('lop_calculation_basis');
                }
            );
        }


        if (! Schema::hasColumn(
            'payroll_management_settings',
            'default_shift_start'
        )) {
            Schema::table(
                'payroll_management_settings',
                function (Blueprint $table) {
                    $table->time('default_shift_start')
                        ->nullable()
                        ->after('attendance_payroll_enabled');
                }
            );
        }


        if (! Schema::hasColumn(
            'payroll_management_settings',
            'default_shift_end'
        )) {
            Schema::table(
                'payroll_management_settings',
                function (Blueprint $table) {
                    $table->time('default_shift_end')
                        ->nullable()
                        ->after('default_shift_start');
                }
            );
        }


        if (! Schema::hasColumn(
            'payroll_management_settings',
            'grace_minutes'
        )) {
            Schema::table(
                'payroll_management_settings',
                function (Blueprint $table) {
                    $table->unsignedSmallInteger('grace_minutes')
                        ->default(10)
                        ->after('default_shift_end');
                }
            );
        }


        if (! Schema::hasColumn(
            'payroll_management_settings',
            'grace_deduction_mode'
        )) {
            Schema::table(
                'payroll_management_settings',
                function (Blueprint $table) {
                    $table->string('grace_deduction_mode', 20)
                        ->default('excess')
                        ->after('grace_minutes');
                }
            );
        }


        if (! Schema::hasColumn(
            'payroll_management_settings',
            'standard_work_minutes_per_day'
        )) {
            Schema::table(
                'payroll_management_settings',
                function (Blueprint $table) {
                    $table->unsignedSmallInteger(
                        'standard_work_minutes_per_day'
                    )
                        ->default(480)
                        ->after('grace_deduction_mode');
                }
            );
        }


        if (! Schema::hasColumn(
            'payroll_management_settings',
            'deduct_late_arrival'
        )) {
            Schema::table(
                'payroll_management_settings',
                function (Blueprint $table) {
                    $table->boolean('deduct_late_arrival')
                        ->default(true)
                        ->after(
                            'standard_work_minutes_per_day'
                        );
                }
            );
        }


        if (! Schema::hasColumn(
            'payroll_management_settings',
            'late_deduction_rounding'
        )) {
            Schema::table(
                'payroll_management_settings',
                function (Blueprint $table) {
                    $table->string(
                        'late_deduction_rounding',
                        30
                    )
                        ->default('Exact Minutes')
                        ->after('deduct_late_arrival');
                }
            );
        }


        /*
        |--------------------------------------------------------------------------
        | PAYROLLS
        |--------------------------------------------------------------------------
        */

        if (! Schema::hasColumn(
            'payrolls',
            'attendance_import_id'
        )) {
            Schema::table(
                'payrolls',
                function (Blueprint $table) {
                    $table->foreignId('attendance_import_id')
                        ->nullable()
                        ->after('paid_days')
                        ->constrained('attendance_imports')
                        ->nullOnDelete();
                }
            );
        }


        if (! Schema::hasColumn(
            'payrolls',
            'attendance_late_minutes'
        )) {
            Schema::table(
                'payrolls',
                function (Blueprint $table) {
                    $table->unsignedInteger(
                        'attendance_late_minutes'
                    )
                        ->default(0)
                        ->after('attendance_import_id');
                }
            );
        }


        if (! Schema::hasColumn(
            'payrolls',
            'attendance_deduction'
        )) {
            Schema::table(
                'payrolls',
                function (Blueprint $table) {
                    $table->decimal(
                        'attendance_deduction',
                        12,
                        2
                    )
                        ->default(0)
                        ->after(
                            'attendance_late_minutes'
                        );
                }
            );
        }
    }


    public function down(): void
    {
        /*
         * Intentionally conservative.
         *
         * This migration completes an existing production-style
         * attendance schema. The down() method does not automatically
         * remove payroll/attendance data to avoid accidental loss.
         *
         * If rollback is ever required, remove these fields manually
         * only after taking a database backup.
         */
    }
};