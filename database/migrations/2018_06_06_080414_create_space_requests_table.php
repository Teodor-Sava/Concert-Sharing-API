<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpaceRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('space_requests', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->unsignedInteger('concert_id');
            $table->foreign('concert_id')->references('id')->on('concerts');
            $table->unsignedInteger('space_id');
            $table->foreign('space_id')->references('id')->on('spaces');
            $table->text('request_message');
            $table->enum('space_status', ['pending', 'accepted', 'rejected']);
            $table->enum('concert_status', ['pending', 'accepted', 'rejected']);
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
        Schema::dropIfExists('space_requests');
    }
}
