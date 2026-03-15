<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsResidentialToDac7EstablishmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dac7_establishments', function (Blueprint $table) {
            $table->boolean('is_residential_address')->nullable()->after('has_establishment');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dac7_establishments', function (Blueprint $table) {
            $table->dropColumn('is_residential_address');
        });
    }
}
