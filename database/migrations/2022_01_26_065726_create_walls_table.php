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
            $table->id();
            $table->string("name", 255);
            $table->string("source", 255);
            $table->string("color", 10);
            $table->json("tags");
            $table->json('categories');
            $table->json('urls');
            $table->string("license", 255)->nullable();
            $table->string("author", 100)->nullable();
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
