<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('images', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->uuid('user_uuid');
            $table->uuid('dish_uuid');
            $table->string('path');
            $table->string('name');
            $table->string('description');
            $table->string('type');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('dish_uuid')->references('uuid')->on('dishes')->cascadeOnDelete();
            $table->foreign('user_uuid')->references('uuid')->on('users')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('images');
    }
};
