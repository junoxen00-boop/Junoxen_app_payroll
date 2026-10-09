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
        | attendance_imports
        |--------------------------------------------------------------------------
        */

        Schema::table('attendance_imports', function (Blueprint $table) {

            if (
                Schema::hasColumn(
                    'attendance_imports',
                    'filename'
                )
            ) {
                $table->string('filename')
                    ->nullable()
                    ->change();
            }

            if (
                ! Schema::hasColumn(
                    'attendance_imports',
                    'original_filename'
                )
            ) {
                $table->string('original_filename')
                    ->nullable()
                    ->after('id');
            }

            if (
                ! Schema::hasColumn(
                    'attendance_imports',
                    'needs_review_count'
                )
            ) {
                $table->unsignedInteger(
                    'needs_review_count'
                )
                    ->default(0)
                    ->after('unmatched_count');
            }

            if (
                ! Schema::hasColumn(
                    'attendance_imports',
                    'confirmed_at'
                )
            ) {
                $table->timestamp(
                    'confirmed_at'
                )
                    ->nullable()
                    ->after('uploaded_by');
            }

            if (
                ! Schema::hasColumn(
                    'attendance_imports',
                    'confirmed_by'
                )
            ) {
                $table->foreignId(
                    'confirmed_by'
                )
                    ->nullable()
                    ->after('confirmed_at')
                    ->constrained('users')
                    ->nullOnDelete();
            }
        });


        /*
        |--------------------------------------------------------------------------
        | attendance_daily_records
        |--------------------------------------------------------------------------
        */

        Schema::table('attendance_daily_records', function (Blueprint $table) {

            if (
                ! Schema::hasColumn(
                    'attendance_daily_records',
                    'source_employee_id'
                )
            ) {
                $table->string(
                    'source_employee_id',
                    80
                )
                    ->nullable()
                    ->after('employee_id');
            }

            if (
                ! Schema::hasColumn(
                    'attendance_daily_records',
                    'source_employee_name'
                )
            ) {
                $table->string(
                    'source_employee_name'
                )
                    ->nullable()
                    ->after('source_employee_id');
            }

            if (
                ! Schema::hasColumn(
                    'attendance_daily_records',
                    'source_department'
                )
            ) {
                $table->string(
                    'source_department'
                )
                    ->nullable()
                    ->after('source_employee_name');
            }

            if (
                ! Schema::hasColumn(
                    'attendance_daily_records',
                    'deductible_minutes'
                )
            ) {
                $table->unsignedInteger(
                    'deductible_minutes'
                )
                    ->default(0)
                    ->after('late_minutes');
            }

            if (
                ! Schema::hasColumn(
                    'attendance_daily_records',
                    'match_method'
                )
            ) {
                $table->string(
                    'match_method',
                    40
                )
                    ->nullable()
                    ->after('review_status');
            }

            if (
                ! Schema::hasColumn(
                    'attendance_daily_records',
                    'review_note'
                )
            ) {
                $table->text(
                    'review_note'
                )
                    ->nullable()
                    ->after('match_method');
            }
        });


        /*
        |--------------------------------------------------------------------------
        | employees
        |--------------------------------------------------------------------------
        */

        Schema::table('employees', function (Blueprint $table) {

            if (
                ! Schema::hasColumn(
                    'employees',
                    'attendance_employee_id'
                )
            ) {
                $table->string(
                    'attendance_employee_id',
                    80
                )
                    ->nullable()
                    ->unique()
                    ->after('employee_id');
            }
        });


        /*
        |--------------------------------------------------------------------------
        | payroll_management_settings
        |--------------------------------------------------------------------------
        */

        Schema::table('payroll_management_settings', function (Blueprint $table) {

            if (
                ! Schema::hasColumn(
                    'payroll_management_settings',
                    'attendance_payroll_enabled'
                )
            ) {
                $table->boolean(
                    'attendance_payroll_enabled'
                )
                    ->default(false);
            }

            if (
                ! Schema::hasColumn(
                    'payroll_management_settings',
                    'default_shift_start'
                )
            ) {
                $table->time(
                    'default_shift_start'
                )
                    ->nullable();
            }

            if (
                ! Schema::hasColumn(
                    'payroll_management_settings',
                    'default_shift_end'
                )
            ) {
                $table->time(
                    'default_shift_end'
                )
                    ->nullable();
            }

            if (
                ! Schema::hasColumn(
                    'payroll_management_settings',
                    'grace_minutes'
                )
            ) {
                $table->unsignedSmallInteger(
                    'grace_minutes'
                )
                    ->default(10);
            }

            if (
                ! Schema::hasColumn(
                    'payroll_management_settings',
                    'grace_deduction_mode'
                )
            ) {
                $table->string(
                    'grace_deduction_mode',
                    20
                )
                    ->default('excess');
            }

            if (
                ! Schema::hasColumn(
                    'payroll_management_settings',
                    'standard_work_minutes_per_day'
                )
            ) {
                $table->unsignedSmallInteger(
                    'standard_work_minutes_per_day'
                )
                    ->default(480);
            }

            if (
                ! Schema::hasColumn(
                    'payroll_management_settings',
                    'deduct_late_arrival'
                )
            ) {
                $table->boolean(
                    'deduct_late_arrival'
                )
                    ->default(true);
            }

            if (
                ! Schema::hasColumn(
                    'payroll_management_settings',
                    'late_deduction_rounding'
                )
            ) {
                $table->string(
                    'late_deduction_rounding',
                    40
                )
                    ->default('Exact Minutes');
            }
        });
    }


    public function down(): void
    {
        /*
         * Intentionally non-destructive.
         *
         * Do not automatically remove attendance columns
         * because they may already contain payroll-related data.
         */
    }
};