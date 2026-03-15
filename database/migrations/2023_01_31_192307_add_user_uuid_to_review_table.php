<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->uuid('user_uuid')->after('uuid');
            $table->foreign('user_uuid')->references('uuid')->on('users');
        });
    }

    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropForeign('reviews_user_uuid_foreign');
            $table->dropColumn('user_uuid');
        });
    }
};
