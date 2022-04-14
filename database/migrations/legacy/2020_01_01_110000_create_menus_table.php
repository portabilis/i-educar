<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMenusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menus', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('parent_id')->nullable();
            $table->string('title');
            $table->string('description')->nullable();
            $table->string('link')->nullable();
            $table->string('icon')->nullable();
            $table->integer('order')->default(99);
            $table->integer('type')->default(1);
            $table->integer('process')->nullable();
            $table->integer('old')->nullable();
            $table->integer('parent_old')->nullable();
            $table->boolean('active')->default(true);
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
        Schema::dropIfExists('menus');
    }
}
