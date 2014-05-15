<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRiders extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('riders', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('class_id')->index();
            $table->foreign('class_id')->references('id')->on('classes')->onUpdate('cascade');
            $table->integer('nationality_id')->index();
            $table->foreign('nationality_id')->references('id')->on('nationalities')->onUpdate('cascade');
            $table->string('no');
            $table->string('name');
            $table->text('comment')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('riders');
    }

}
