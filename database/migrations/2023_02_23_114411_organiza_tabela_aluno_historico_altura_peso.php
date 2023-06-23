<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up()
    {
        \DB::statement('ALTER TABLE IF EXISTS pmieducar.aluno_historico_altura_peso DROP CONSTRAINT IF EXISTS pmieducar_aluno_historico_altura_peso_ref_cod_aluno_foreign;');
        Schema::table('aluno_historico_altura_peso', function (Blueprint $table) {
            $table->increments('id');
            $table->foreign('ref_cod_aluno')->references('cod_aluno')->on('pmieducar.aluno');
        });
    }

    public function down()
    {
        \DB::statement('ALTER TABLE IF EXISTS pmieducar.aluno_historico_altura_peso DROP CONSTRAINT IF EXISTS aluno_historico_altura_peso_pkey;');
        \DB::statement('ALTER TABLE IF EXISTS pmieducar.aluno_historico_altura_peso DROP CONSTRAINT IF EXISTS aluno_historico_altura_peso_ref_cod_aluno_foreign;');
        \DB::statement('ALTER TABLE IF EXISTS pmieducar.aluno_historico_altura_peso ADD CONSTRAINT pmieducar_aluno_historico_altura_peso_ref_cod_aluno_foreign FOREIGN KEY (ref_cod_aluno) REFERENCES pmieducar.aluno (cod_aluno) MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION;');

        Schema::table('aluno_historico_altura_peso', function (Blueprint $table) {
            $table->dropColumn('id');
        });
    }
};
