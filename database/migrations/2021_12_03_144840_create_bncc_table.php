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
            $table->char('codigo', 8);
            $table->string('habilidade', 2048);
            $table->char('campo_experiencia')->nullable();
            $table->char('unidade_tematica')->nullable();
            $table->integer('componente_curricular_id')->nullable();
        });

        DB::statement('ALTER TABLE modules.bncc ADD COLUMN serie_ids integer[]');
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
