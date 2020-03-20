<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePersonHasPlaceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('public.person_has_place', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('person_id');
            $table->integer('place_id');
            $table->integer('type');
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['person_id', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('public.person_has_place');
    }
}
