<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wallet_lines', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->uuid('wallet_uuid');
            $table->uuid('order_uuid');
            $table->float('amount');
            $table->unsignedTinyInteger('state')->default(1);
            $table->timestamps();

            $table->foreign('wallet_uuid')->references('uuid')->on('wallets')->cascadeOnDelete();
            $table->foreign('order_uuid')->references('uuid')->on('orders')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wallet_lines');
    }
};
