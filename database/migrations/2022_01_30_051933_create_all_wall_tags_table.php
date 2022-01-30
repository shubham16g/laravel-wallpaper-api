<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAllWallTagsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('all_wall_tags', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('wall_id');
            $table->foreign('wall_id')->references('wall_id')->on('walls');
            $table->unsignedBigInteger('all_tag_id');
            $table->foreign('all_tag_id')->references('all_tag_id')->on('all_tags');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('all_wall_tags');
    }
}
