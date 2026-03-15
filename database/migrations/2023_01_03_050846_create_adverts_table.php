<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('adverts', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->uuid('dish_uuid');
            $table->unsignedTinyInteger('valuta')->default(1)->nullable();
            $table->unsignedTinyInteger('portion_amount');
            // $table->float('portion_price');
            $table->date('pickup_date');
            $table->time('pickup_from');
            $table->time('pickup_to');
            $table->date('order_date');
            $table->time('order_time');
            $table->timestamp('published')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('dish_uuid')->references('uuid')->on('dishes')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('adverts');
    }
};
