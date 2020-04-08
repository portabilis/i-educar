<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableMigratedDisciplines extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('public.migrated_disciplines', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('old_discipline_id');
            $table->integer('new_discipline_id');
            $table->integer('grade_id')->nullable();
            $table->integer('year');
            $table->integer('created_by')->nullable();
            $table->timestamps();

            $table->foreign('old_discipline_id')->references('id')->on('modules.componente_curricular');
            $table->foreign('new_discipline_id')->references('id')->on('modules.componente_curricular');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('public.migrated_disciplines');
    }
}
