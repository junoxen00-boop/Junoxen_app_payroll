<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendance_imports', function (Blueprint $table) {
            if (!Schema::hasColumn(
                'attendance_imports',
                'original_filename'
            )) {
                $table->string('original_filename')
                    ->nullable()
                    ->after('id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('attendance_imports', function (Blueprint $table) {
            if (Schema::hasColumn(
                'attendance_imports',
                'original_filename'
            )) {
                $table->dropColumn('original_filename');
            }
        });
    }
};