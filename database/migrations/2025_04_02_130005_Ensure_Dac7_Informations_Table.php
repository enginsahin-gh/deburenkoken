<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class EnsureDac7InformationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasTable('dac7_informations')) {
            Schema::create('dac7_informations', function (Blueprint $table) {
                $table->id();
                $table->string('user_id');
                $table->foreign('user_id')->references('uuid')->on('users')->onDelete('cascade');
                $table->boolean('information_provided')->default(true);
                $table->timestamps();
            });
        } else {
            // Controleer of de kolom information_provided bestaat en is een boolean
            if (! Schema::hasColumn('dac7_informations', 'information_provided')) {
                Schema::table('dac7_informations', function (Blueprint $table) {
                    $table->boolean('information_provided')->default(true);
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {}
}
