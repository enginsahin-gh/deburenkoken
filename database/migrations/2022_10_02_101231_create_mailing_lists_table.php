<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mailing_lists', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->uuid('cook_uuid');
            $table->uuid('client_uuid');
            $table->timestamps();

            $table->foreign('cook_uuid')->references('uuid')->on('cooks')->cascadeOnDelete();
            $table->foreign('client_uuid')->references('uuid')->on('clients')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mailing_lists');
    }
};
