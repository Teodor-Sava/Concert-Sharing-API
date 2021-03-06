<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConcertRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('concert_requests', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('concert_id');
            $table->foreign('concert_id')->references('id')->on('concerts');
            $table->foreign('user_id')->references('id')->on('users');
            $table->unsignedInteger('band_id');
            $table->foreign('band_id')->references('id')->on('bands');
            $table->enum('band_status', ['pending', 'accepted', 'rejected']);
            $table->enum('concert_status', ['pending', 'accepted', 'rejected']);
            $table->text('request_message');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('concert_requests');
    }
}
