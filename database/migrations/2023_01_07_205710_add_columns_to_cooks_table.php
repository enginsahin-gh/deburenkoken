<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cooks', function (Blueprint $table) {
            $table->decimal('lat', 10, 8)->after('user_uuid');
            $table->decimal('long', 11, 8)->after('lat');
        });
    }

    public function down(): void
    {
        Schema::table('cooks', function (Blueprint $table) {
            $table->dropColumn('lat');
            $table->dropColumn('long');
        });
    }
};
