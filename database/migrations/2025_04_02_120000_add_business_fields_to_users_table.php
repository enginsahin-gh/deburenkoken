<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBusinessFieldsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'kvk_nummer')) {
                $table->string('kvk_nummer')->nullable();
            }
            if (! Schema::hasColumn('users', 'rsin')) {
                $table->string('rsin')->nullable();
            }
            if (! Schema::hasColumn('users', 'vestigingsnummer')) {
                $table->string('vestigingsnummer')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['kvk_nummer', 'rsin', 'vestigingsnummer']);
        });
    }
}
