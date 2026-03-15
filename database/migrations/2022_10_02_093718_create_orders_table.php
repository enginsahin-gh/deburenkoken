<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->uuid('user_uuid');
            $table->uuid('dish_uuid');
            $table->uuid('client_uuid');
            $table->unsignedInteger('portion_amount');
            $table->timestamp('expected_pickup_time');
            $table->string('remarks')->nullable();
            $table->unsignedTinyInteger('payment_state')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('dish_uuid')->references('uuid')->on('dishes')->cascadeOnDelete();
            $table->foreign('client_uuid')->references('uuid')->on('users')->cascadeOnDelete();
            $table->foreign('user_uuid')->references('uuid')->on('users')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
