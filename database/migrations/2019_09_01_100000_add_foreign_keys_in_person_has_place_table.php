<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignKeysInPersonHasPlaceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('public.person_has_place', function (Blueprint $table) {
            $table->foreign('person_id')->on('cadastro.pessoa')->references('idpes');
            $table->foreign('place_id')->on('public.places')->references('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('public.person_has_place', function (Blueprint $table) {
            $table->dropForeign(['person_id']);
            $table->dropForeign(['place_id']);
        });
    }
}
