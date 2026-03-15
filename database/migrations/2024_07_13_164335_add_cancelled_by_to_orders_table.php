<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCancelledByToOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('cancelled_by')->nullable()->after('status');
            // Hierboven voegen we een nieuwe kolom 'cancelled_by' toe aan de 'orders' tabel.
            // nullable() geeft aan dat het veld NULL kan zijn indien er geen annuleerder is opgegeven.
            // after('status') plaatst de nieuwe kolom na de bestaande 'status' kolom.
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('cancelled_by');
            // Als je de migratie ongedaan wilt maken, verwijder dan de 'cancelled_by' kolom.
        });
    }
}
