<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dishes', function (Blueprint $table) {
            $table->string('title', 150)->change();
            $table->dropColumn('avatar');
            $table->dropColumn('portion_amount');
            $table->dropColumn('portion_price');
            $table->dropColumn('valuta');
            $table->dropColumn('pickup_from');
            $table->dropColumn('pickup_to');
            $table->dropColumn('order_deadline');
            $table->dropColumn('published');
            $table->dropColumn('is_cancelled');
        });
    }

    public function down(): void
    {
        Schema::table('dishes', function (Blueprint $table) {
            $table->string('title', 25)->change();
            $table->string('avatar')->nullable();
            $table->integer('portion_amount')->nullable()->after('spice_level');
            $table->float('portion_price')->nullable()->after('portion_amount');
            $table->string('valuta')->nullable()->after('portion_price');
            $table->timestamp('pickup_from')->nullable()->after('valuta');
            $table->timestamp('pickup_to')->nullable()->after('pickup_from');
            $table->timestamp('order_deadline')->nullable()->after('pickup_to');
            $table->timestamp('published')->nullable()->after('order_deadline');
            $table->boolean('is_cancelled')->default(false)->after('published');
        });
    }
};
