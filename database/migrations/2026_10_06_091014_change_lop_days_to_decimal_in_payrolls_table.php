<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table(
            'payrolls',
            function (Blueprint $table) {

                $table
                    ->decimal(
                        'lop_days',
                        5,
                        2
                    )
                    ->default(0)
                    ->change();
            }
        );
    }

    public function down(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Intentionally left unchanged
        |--------------------------------------------------------------------------
        |
        | Converting lop_days back to an integer would destroy
        | valid half-day values such as 0.50 and 1.50.
        |
        | Therefore this migration does not automatically
        | downgrade the column.
        |
        */
    }
};