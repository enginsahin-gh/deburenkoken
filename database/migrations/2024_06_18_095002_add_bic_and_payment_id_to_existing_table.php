<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBicAndPaymentIdToExistingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('banking', function (Blueprint $table) {
            $table->string('bic')->nullable()->after('iban');
            $table->string('payment_id')->nullable()->after('bic');
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
            $table->dropColumn('bic');
            $table->dropColumn('payment_id');
        });
    }
}
