<?php

use App\Models\Department;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();

            $table->string('employee_id')->unique()->nullable();

            $table->string('full_name');
            $table->string('email')->unique();
            $table->string('mobile_number');

            $table->foreignIdFor(Department::class)
                ->constrained()
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->string('designation');
            $table->date('joining_date');
            $table->enum('status', ['Active', 'Inactive'])->default('Active');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};