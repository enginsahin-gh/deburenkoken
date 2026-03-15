<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('banking', function (Blueprint $table) {
            $table->dropColumn(['house_number', 'street', 'postal_code', 'city']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('banking', function (Blueprint $table) {
            $table->string('house_number');
            $table->string('street');
            $table->string('postal_code');
            $table->string('city');
        });
    }
};
