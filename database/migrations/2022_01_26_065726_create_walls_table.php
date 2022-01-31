<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWallsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('walls', function (Blueprint $table) {
            $table->id("wall_id");
            $table->string("source", 255);
            $table->string("color", 10);
            $table->json('urls');
            $table->string("license", 255)->nullable();
            $table->unsignedBigInteger('author_id')->nullable();
            $table->foreign('author_id')->references('author_id')->on('authors');
            $table->integer("rotation")->default(0);
            $table->enum('flip', ['h', 'v', 'hv'])->nullable();
            $table->integer("downloads")->default(0);
            $table->integer("coins")->default(0);
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
        Schema::dropIfExists('walls');
    }
}
