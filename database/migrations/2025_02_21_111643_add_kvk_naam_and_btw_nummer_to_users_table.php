<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddKvkNaamAndBtwNummerToUsersTable extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('type_thuiskok')->nullable();
            $table->string('kvk_naam')->nullable();
            $table->string('btw_nummer')->nullable();
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'type_thuiskok')) {
                $table->dropColumn('type_thuiskok');
            }
            if (Schema::hasColumn('users', 'kvk_naam')) {
                $table->dropColumn('kvk_naam');
            }
            if (Schema::hasColumn('users', 'btw_nummer')) {
                $table->dropColumn('btw_nummer');
            }
        });
    }
}
