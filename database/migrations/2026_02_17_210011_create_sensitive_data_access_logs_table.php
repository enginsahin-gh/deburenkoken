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
        Schema::create('sensitive_data_access_logs', function (Blueprint $table) {
            $table->uuid()->primary();
            $table->uuid('admin_user_uuid');
            $table->uuid('target_user_uuid');
            $table->string('field_type');
            $table->string('ip_address')->nullable();
            $table->timestamps();

            $table->foreign('admin_user_uuid')->references('uuid')->on('users');
            $table->foreign('target_user_uuid')->references('uuid')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sensitive_data_access_logs');
    }
};
