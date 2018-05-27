<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyPropertiesConcertTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('concerts', function (Blueprint $table) {
            $table->dropForeign('concerts_band_id_foreign');
            $table->dropForeign('concerts_space_id_foreign');
            $table->unsignedInteger('band_id')->nullable()->change();
            $table->unsignedInteger('space_id')->nullable()->change();
            $table->date('concert_public')->nullable();

            $table->foreign('band_id')->references('id')->on('bands');
            $table->foreign('space_id')->references('id')->on('spaces');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
