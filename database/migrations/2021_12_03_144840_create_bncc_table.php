<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBNCCTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('modules.bncc', function (Blueprint $table) {
            $table->id();
            $table->char('code');
            $table->string('habilidade', 500);
            $table->char('campo_experiencia')->nullable();
            $table->char('unidade_tematica')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('modules.bncc');
    }
}
