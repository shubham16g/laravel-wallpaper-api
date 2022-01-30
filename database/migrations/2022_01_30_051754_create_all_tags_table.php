<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAllTagsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('all_tags', function (Blueprint $table) {
            $table->id("all_tag_id");
            $table->string("name", 255);
            $table->enum('type', ['tag', 'category', 'color'])->default('tag');
            $table->integer('popularity')->unsigned()->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('all_tags');
    }
}
