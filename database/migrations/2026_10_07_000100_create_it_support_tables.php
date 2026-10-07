<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('it_support_tickets', function (Blueprint $table) {
            $table->id();

            $table->string('ticket_number', 50)->unique();

            $table->foreignId('employee_id')
                ->constrained('employees')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignId('department_id')
                ->nullable()
                ->constrained('departments')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->string('subject', 255);

            $table->string('category', 100);

            $table->text('description');

            $table->string('priority', 20)
                ->default('Medium');

            $table->string('status', 30)
                ->default('Open');

            $table->foreignId('assigned_to')
                ->nullable()
                ->constrained(
                    table: 'employees',
                    indexName: 'it_support_tickets_assigned_to_foreign'
                )
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->text('resolution')
                ->nullable();

            $table->foreignId('resolved_by')
                ->nullable()
                ->constrained(
                    table: 'users',
                    indexName: 'it_support_tickets_resolved_by_foreign'
                )
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->timestamp('resolved_at')
                ->nullable();

            $table->timestamps();

            $table->index('status');
            $table->index('priority');
            $table->index('category');
            $table->index('created_at');
        });


        Schema::create('it_support_ticket_comments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('ticket_id')
                ->constrained('it_support_tickets')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->text('message');

            $table->string('type', 30)
                ->default('comment');

            $table->timestamps();

            $table->index('created_at');
        });
    }


    public function down(): void
    {
        Schema::dropIfExists(
            'it_support_ticket_comments'
        );

        Schema::dropIfExists(
            'it_support_tickets'
        );
    }
};