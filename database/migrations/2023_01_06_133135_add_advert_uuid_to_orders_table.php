<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign('orders_client_uuid_foreign');
            $table->foreign('client_uuid')->references('uuid')->on('clients')->cascadeOnDelete();

            $table->uuid('advert_uuid')->after('client_uuid');
            $table->foreign('advert_uuid')->references('uuid')->on('adverts')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign('orders_client_uuid_foreign');
            $table->foreign('client_uuid')->references('uuid')->on('users')->cascadeOnDelete();

            $table->dropForeign('orders_advert_uuid_foreign');
            $table->dropColumn('advert_uuid');
        });
    }
};
