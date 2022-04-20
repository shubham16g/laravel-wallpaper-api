<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bases', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('featured');
            $table->foreign('featured')->references('wall_id')->on('walls');
            $table->string('featured_title', 255)->nullable();
            $table->string('featured_description', 255)->nullable();
            $table->integer('current_version')->unsigned()->default(1);
            $table->integer('immediate_update')->unsigned()->nullable();
            $table->string('play_store_url_short',255)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bases');
    }
}
