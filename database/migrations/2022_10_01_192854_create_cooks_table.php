<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cooks', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->uuid('user_uuid');
            $table->string('street', 50);
            $table->unsignedSmallInteger('house_number');
            $table->string('addition', 10)->nullable();
            $table->string('postal_code', 7);
            $table->string('city', 50);
            $table->string('country', 5);
            $table->text('description')->nullable();
            $table->boolean('mail_order')->default(true);
            $table->boolean('mail_cancel')->default(true);
            $table->boolean('mail_self')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_uuid')->references('uuid')->on('users')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cooks');
    }
};
