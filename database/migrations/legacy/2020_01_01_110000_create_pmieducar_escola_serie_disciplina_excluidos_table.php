<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreatePmieducarEscolaSerieDisciplinaExcluidosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pmieducar.escola_serie_disciplina_excluidos', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('ref_ref_cod_serie');
            $table->integer('ref_ref_cod_escola');
            $table->integer('ref_cod_disciplina');
            $table->integer('ativo');
            $table->integer('carga_horaria')->nullable();
            $table->integer('etapas_especificas')->nullable();
            $table->string('etapas_utilizadas')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        DB::unprepared(
            '
                ALTER TABLE pmieducar.escola_serie_disciplina_excluidos 
                ADD COLUMN anos_letivos int2[] NOT NULL DEFAULT \'{}\'::smallint[]
            '
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pmieducar.escola_serie_disciplina_excluidos');
    }
}
