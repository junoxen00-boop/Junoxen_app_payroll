<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('task_assignments', function (Blueprint $table) {

            $table->string('attachment')->nullable()->after('remarks');

           $table->string('review_status')
      ->default('Pending')
      ->after('attachment');

            $table->text('review_comment')->nullable()->after('review_status');

            $table->foreignId('reviewed_by')
                ->nullable()
                ->after('review_comment')
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('reviewed_at')
                ->nullable()
                ->after('reviewed_by');

        });
    }

    public function down(): void
    {
        Schema::table('task_assignments', function (Blueprint $table) {

            $table->dropForeign(['reviewed_by']);

            $table->dropColumn([
                'attachment',
                'review_status',
                'review_comment',
                'reviewed_by',
                'reviewed_at',
            ]);

        });
    }
};