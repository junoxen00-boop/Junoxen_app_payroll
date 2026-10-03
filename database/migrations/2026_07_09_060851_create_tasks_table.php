<?php

use App\Models\User;
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
        Schema::create('tasks', function (Blueprint $table): void {
            $table->id();

            $table->string('task_code')->unique();

            $table->string('title');
            $table->text('description')->nullable();

            $table->enum('priority', [
                'Low',
                'Medium',
                'High',
                'Critical',
            ])->default('Medium');

            $table->date('start_date')->nullable();
            $table->date('due_date')->nullable();

            $table->foreignIdFor(User::class, 'created_by')
                ->constrained('users')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->timestamps();

            $table->index('priority');
            $table->index('start_date');
            $table->index('due_date');
            $table->index('created_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};