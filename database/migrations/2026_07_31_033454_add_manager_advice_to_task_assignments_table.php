<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('task_assignments') || Schema::hasColumn('task_assignments', 'manager_advice')) {
            return;
        }

        Schema::table('task_assignments', function (Blueprint $table) {
            $table->text('manager_advice')->nullable()->after('remarks');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('task_assignments') || ! Schema::hasColumn('task_assignments', 'manager_advice')) {
            return;
        }

        Schema::table('task_assignments', function (Blueprint $table) {
            $table->dropColumn('manager_advice');
        });
    }
};
