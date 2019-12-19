<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEducacensoImport extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('educacenso_import', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('year');
            $table->string('school');
            $table->integer('user_id');
            $table->boolean('finished');
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
        Schema::dropIfExists('educacenso_import');
    }
}
