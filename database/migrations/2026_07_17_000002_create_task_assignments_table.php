<?php

use App\Models\Employee;
use App\Models\Task;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('task_assignments', function (Blueprint $table): void {
            $table->id();

            $table->foreignIdFor(Task::class)
                ->constrained('tasks')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignIdFor(Employee::class)
                     ->constrained('employees')
                     ->cascadeOnUpdate()
                     ->cascadeOnDelete();

            $table->enum('status', [
                'Pending',
                'In Progress',
                'Completed',
            ])->default('Pending');

            $table->unsignedTinyInteger('progress')->default(0);

            $table->text('remarks')->nullable();

            $table->text('manager_advice')->nullable();

            $table->timestamp('completed_at')->nullable();

            $table->timestamps();

            $table->unique(['task_id', 'employee_id']);

            $table->index('task_id');
            $table->index('employee_id');
            $table->index('status');
            $table->index('progress');
            $table->index('completed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_assignments');
    }
};