<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('cook_profile_description', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('payments', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('wallet_lines', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('wallets', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn('deleted_at');
        });
        Schema::table('cook_profile_description', function (Blueprint $table) {
            $table->dropColumn('deleted_at');
        });
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn('deleted_at');
        });
        Schema::table('wallet_lines', function (Blueprint $table) {
            $table->dropColumn('deleted_at');
        });
        Schema::table('wallets', function (Blueprint $table) {
            $table->dropColumn('deleted_at');
        });
    }
};
