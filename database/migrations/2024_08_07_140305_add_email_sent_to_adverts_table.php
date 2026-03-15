<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEmailSentToAdvertsTable extends Migration
{
    public function up()
    {
        Schema::table('adverts', function (Blueprint $table) {
            $table->boolean('email_sent')->default(false);
        });
    }

    public function down()
    {
        Schema::table('adverts', function (Blueprint $table) {
            $table->dropColumn('email_sent');
        });
    }
}
