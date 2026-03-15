<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Maak de dac7_informations tabel als die nog niet bestaat
        if (! Schema::hasTable('dac7_informations')) {
            Schema::create('dac7_informations', function (Blueprint $table) {
                $table->id();
                $table->foreignUuid('user_id')->constrained('users', 'uuid')->onDelete('cascade');
                $table->boolean('information_provided')->default(false);
                $table->timestamps();
            });
        }

        // Voeg e-mail tracking velden toe aan users tabel
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'dac7_warning_email_sent')) {
                $table->boolean('dac7_warning_email_sent')->default(false)->after('email');
            }

            if (! Schema::hasColumn('users', 'dac7_required_email_sent')) {
                $table->boolean('dac7_required_email_sent')->default(false)->after('dac7_warning_email_sent');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'dac7_warning_email_sent')) {
                $table->dropColumn('dac7_warning_email_sent');
            }

            if (Schema::hasColumn('users', 'dac7_required_email_sent')) {
                $table->dropColumn('dac7_required_email_sent');
            }
        });

        Schema::dropIfExists('dac7_informations');
    }
};
