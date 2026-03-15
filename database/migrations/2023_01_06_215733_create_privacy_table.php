<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('privacy', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->uuid('user_uuid');
            $table->tinyInteger('place')->default(1);
            $table->tinyInteger('street')->default(1);
            $table->tinyInteger('house_number')->default(1);
            $table->tinyInteger('phone')->default(1);
            $table->tinyInteger('email')->default(1);
            $table->tinyInteger('sold_portions')->default(1);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_uuid')->references('uuid')->on('users')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('privacy');
    }
};
