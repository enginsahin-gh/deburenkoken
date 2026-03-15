<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dishes', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->uuid('user_uuid');
            $table->uuid('cook_uuid')->nullable();
            $table->string('title', 25);
            $table->longText('description');
            $table->string('avatar');
            $table->float('portion_price');
            $table->boolean('is_vegetarian')->default(false);
            $table->boolean('is_vegan')->default(false);
            $table->boolean('is_halal')->default(false);
            $table->boolean('has_alcohol')->default(false);
            $table->boolean('has_gluten')->default(true);
            $table->boolean('has_lactose')->default(true);
            $table->tinyInteger('spice_level');
            $table->integer('portion_amount');

            $table->string('valuta');
            $table->timestamp('pickup_from')->nullable();
            $table->timestamp('pickup_to')->nullable();
            $table->timestamp('order_deadline')->nullable();
            $table->timestamp('published')->nullable();
            $table->boolean('is_cancelled')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('cook_uuid')->references('uuid')->on('cooks')->cascadeOnDelete();
            $table->foreign('user_uuid')->references('uuid')->on('users')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dishes');
    }
};
