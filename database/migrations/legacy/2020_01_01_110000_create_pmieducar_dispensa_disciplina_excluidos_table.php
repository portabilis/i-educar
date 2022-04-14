<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePmieducarDispensaDisciplinaExcluidosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pmieducar.dispensa_disciplina_excluidos', function (Blueprint $table) {
            $table->integer('cod_dispensa')->index();
            $table->integer('ref_cod_matricula');
            $table->integer('ref_cod_disciplina');
            $table->integer('ref_cod_escola');
            $table->integer('ref_cod_serie');
            $table->integer('ref_usuario_exc')->nullable();
            $table->integer('ref_usuario_cad');
            $table->integer('ref_cod_tipo_dispensa');
            $table->timestamp('data_cadastro');
            $table->timestamp('data_exclusao')->nullable();
            $table->integer('ativo');
            $table->text('observacao')->nullable();
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
        Schema::dropIfExists('pmieducar.dispensa_disciplina_excluidos');
    }
}
