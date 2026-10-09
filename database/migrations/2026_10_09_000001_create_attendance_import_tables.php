<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_imports', function (Blueprint $table) {
            $table->id();

            $table->string('filename');

            $table->unsignedTinyInteger('payroll_month');
            $table->unsignedSmallInteger('payroll_year');

            $table->foreignId('uploaded_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('status', 30)
                ->default('Pending');

            $table->unsignedInteger('row_count')
                ->default(0);

            $table->unsignedInteger('matched_count')
                ->default(0);

            $table->unsignedInteger('unmatched_count')
                ->default(0);

            $table->timestamp('imported_at')
                ->nullable();

            $table->timestamps();

            $table->index([
                'payroll_year',
                'payroll_month',
            ]);
        });


        Schema::create('attendance_import_rows', function (Blueprint $table) {
            $table->id();

            $table->foreignId('attendance_import_id')
                ->constrained('attendance_imports')
                ->cascadeOnDelete();

            $table->foreignId('employee_id')
                ->nullable()
                ->constrained('employees')
                ->nullOnDelete();

            $table->string('attendance_employee_id')
                ->nullable();

            $table->string('employee_name')
                ->nullable();

            $table->string('department_name')
                ->nullable();

            $table->string('match_status', 30)
                ->default('Needs Review');

            $table->text('raw_row_data')
                ->nullable();

            $table->timestamps();

            $table->index('match_status');
        });


        Schema::create('attendance_daily_records', function (Blueprint $table) {
            $table->id();

            $table->foreignId('attendance_import_id')
                ->constrained('attendance_imports')
                ->cascadeOnDelete();

            $table->foreignId('attendance_import_row_id')
                ->nullable()
                ->constrained('attendance_import_rows')
                ->nullOnDelete();

            $table->foreignId('employee_id')
                ->nullable()
                ->constrained('employees')
                ->nullOnDelete();

            $table->date('attendance_date');

            $table->text('raw_punches')
                ->nullable();

            $table->time('first_entry')
                ->nullable();

            $table->time('last_exit')
                ->nullable();

            $table->unsignedInteger('worked_minutes')
                ->default(0);

            $table->unsignedInteger('scheduled_minutes')
                ->default(0);

            $table->unsignedInteger('late_minutes')
                ->default(0);

            $table->unsignedInteger('early_exit_minutes')
                ->default(0);

            $table->unsignedInteger('payable_missing_minutes')
                ->default(0);

            $table->string('attendance_status', 40)
                ->default('Attendance Missing');

            $table->string('review_status', 30)
                ->default('Needs Review');

            $table->text('override_reason')
                ->nullable();

            $table->foreignId('modified_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('modified_at')
                ->nullable();

            $table->timestamps();

            $table->unique([
                'attendance_import_id',
                'employee_id',
                'attendance_date',
            ], 'attendance_daily_unique');

            $table->index('attendance_date');
            $table->index('review_status');
            $table->index('attendance_status');
        });
    }


    public function down(): void
    {
        Schema::dropIfExists(
            'attendance_daily_records'
        );

        Schema::dropIfExists(
            'attendance_import_rows'
        );

        Schema::dropIfExists(
            'attendance_imports'
        );
    }
};