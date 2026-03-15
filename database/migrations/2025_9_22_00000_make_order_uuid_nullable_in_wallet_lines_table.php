<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('wallet_lines', function (Blueprint $table) {
            // Drop the foreign key first
            $table->dropForeign(['order_uuid']);

            // Make order_uuid nullable
            $table->uuid('order_uuid')->nullable()->change();

            // Recreate the foreign key with nullable support
            $table->foreign('order_uuid')->references('uuid')->on('orders')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('wallet_lines', function (Blueprint $table) {
            // Drop the foreign key
            $table->dropForeign(['order_uuid']);

            // Make order_uuid not nullable again
            $table->uuid('order_uuid')->nullable(false)->change();

            // Recreate the foreign key
            $table->foreign('order_uuid')->references('uuid')->on('orders')->onDelete('cascade');
        });
    }
};
