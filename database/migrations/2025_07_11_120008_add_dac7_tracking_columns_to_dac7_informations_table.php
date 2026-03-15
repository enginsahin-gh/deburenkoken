<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDac7TrackingColumnsToDac7InformationsTable extends Migration
{
    public function up()
    {
        Schema::table('dac7_informations', function (Blueprint $table) {
            $table->datetime('dac7_threshold_reached_at')->nullable()->after('information_provided');
            $table->string('dac7_form_link')->nullable()->after('dac7_threshold_reached_at');
        });
    }

    public function down()
    {
        Schema::table('dac7_informations', function (Blueprint $table) {
            $table->dropColumn(['dac7_threshold_reached_at', 'dac7_form_link']);
        });
    }
}
