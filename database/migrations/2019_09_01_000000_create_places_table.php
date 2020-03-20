<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlacesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('public.places', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('city_id');
            $table->string('address');
            $table->string('number')->nullable();
            $table->string('complement')->nullable();
            $table->string('neighborhood');
            $table->string('postal_code');
            $table->float('latitude')->nullable();
            $table->float('longitude')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('public.places');
    }
}
