<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReasonToKasbonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('kasbons', function (Blueprint $table) {
            //
            // Menambahkan kolom 'reason' untuk menyimpan alasan penolakan kasbon
            $table->string('reason')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('kasbons', function (Blueprint $table) {
            //
            // Menghapus kolom 'reason' jika migrasi dibatalkan
            $table->dropColumn('reason');
        });
    }
}
