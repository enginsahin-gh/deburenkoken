<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIdDocumentsToBankingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('banking', function (Blueprint $table) {
            $table->string('id_front')->nullable();
            $table->string('id_back')->nullable();
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
            $table->dropColumn(['id_front', 'id_back']);
        });
    }
}
