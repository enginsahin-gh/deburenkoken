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
        Schema::create('website_status', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_online')->default(true);
            $table->timestamps();
        });

        // Controleer eerst of er al een record bestaat
        if (! DB::table('website_status')->exists()) {
            // Voeg alleen een record toe als de tabel leeg is
            DB::table('website_status')->insert([
                'is_online' => true,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('website_status');
    }
};
