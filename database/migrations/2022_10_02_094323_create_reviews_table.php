<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->uuid('order_uuid');
            $table->uuid('client_uuid');
            $table->boolean('anonymous')->default(false);
            $table->tinyInteger('rating');
            $table->longText('review');
            $table->string('image')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('order_uuid')->references('uuid')->on('orders')->cascadeOnDelete();
            $table->foreign('client_uuid')->references('uuid')->on('clients')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
