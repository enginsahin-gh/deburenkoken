<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_profile', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->string('firstname', 75);
            $table->string('insertion', 25)->nullable();
            $table->string('lastname', 75);
            $table->string('phone_number', 15)->nullable();
            $table->string('mobile_number', 15)->nullable();
            $table->uuid('user_uuid');
            $table->foreign('user_uuid')->references('uuid')->on('users')->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_profile');
    }
};
