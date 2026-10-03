<?php

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
        Schema::create('settings', function (Blueprint $table) {

            $table->id();

            // Company Information
            $table->string('company_name')->nullable();
            $table->string('company_logo')->nullable();
            $table->string('company_email')->nullable();
            $table->string('company_phone')->nullable();
            $table->string('company_website')->nullable();
            $table->text('company_address')->nullable();

            // System Settings
            $table->string('timezone')->default('Asia/Kolkata');
            $table->string('date_format')->default('d-m-Y');

            // Email Settings
            $table->string('sender_name')->nullable();
            $table->string('sender_email')->nullable();
            $table->string('reply_to_email')->nullable();
            $table->boolean('email_notifications')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};