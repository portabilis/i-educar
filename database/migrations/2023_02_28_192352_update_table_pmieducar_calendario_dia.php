<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up()
    {
        DB::unprepared('ALTER TABLE IF EXISTS modules.calendario_turma DROP CONSTRAINT IF EXISTS calendario_turma_calendario_dia_fk;');
        DB::unprepared('ALTER TABLE IF EXISTS modules.calendario_turma DROP CONSTRAINT IF EXISTS modules_calendario_turma_calendario_ano_letivo_id_mes_dia_forei;');
        DB::unprepared('ALTER TABLE IF EXISTS pmieducar.calendario_dia_anotacao DROP CONSTRAINT IF EXISTS calendario_dia_anotacao_ref_ref_cod_calendario_ano_letivo_fkey;');
        DB::unprepared('ALTER TABLE IF EXISTS pmieducar.calendario_dia_anotacao DROP CONSTRAINT IF EXISTS pmieducar_calendario_dia_anotacao_ref_ref_cod_calendario_ano_le;');

        DB::unprepared('ALTER TABLE IF EXISTS pmieducar.calendario_dia DROP CONSTRAINT IF EXISTS calendario_dia_pkey;');
        DB::unprepared('ALTER TABLE IF EXISTS pmieducar.calendario_dia DROP CONSTRAINT IF EXISTS calendario_dia_ref_cod_calendario_ano_letivo_fkey;');
        DB::unprepared('ALTER TABLE IF EXISTS pmieducar.calendario_dia DROP CONSTRAINT IF EXISTS calendario_dia_ref_cod_calendario_dia_motivo_fkey;');
        DB::unprepared('ALTER TABLE IF EXISTS pmieducar.calendario_dia DROP CONSTRAINT IF EXISTS pmieducar_calendario_dia_ref_cod_calendario_ano_letivo_foreign;');
        DB::unprepared('ALTER TABLE IF EXISTS pmieducar.calendario_dia DROP CONSTRAINT IF EXISTS pmieducar_calendario_dia_ref_cod_calendario_dia_motivo_foreign;');
        DB::unprepared('ALTER TABLE IF EXISTS pmieducar.calendario_dia DROP CONSTRAINT IF EXISTS calendario_dia_ref_usuario_exc_fkey;');
        DB::unprepared('ALTER TABLE IF EXISTS pmieducar.calendario_dia DROP CONSTRAINT IF EXISTS calendario_dia_ref_usuario_cad_fkey;');
        DB::unprepared('DROP INDEX IF EXISTS pmieducar.i_calendario_dia_ativo;');
        DB::unprepared('DROP INDEX IF EXISTS pmieducar.i_calendario_dia_dia;');
        DB::unprepared('DROP INDEX IF EXISTS pmieducar.i_calendario_dia_mes;');
        DB::unprepared('DROP INDEX IF EXISTS pmieducar.i_calendario_dia_ref_cod_calendario_dia_motivo;');
        DB::unprepared('DROP INDEX IF EXISTS pmieducar.i_calendario_dia_ref_usuario_cad;');

        Schema::table('pmieducar.calendario_dia', function (Blueprint $table) {
            $table->increments('id');
            $table->foreign('ref_cod_calendario_ano_letivo')->references('cod_calendario_ano_letivo')->on('pmieducar.calendario_ano_letivo');
            $table->foreign('ref_cod_calendario_dia_motivo')->references('cod_calendario_dia_motivo')->on('pmieducar.calendario_dia_motivo');
            $table->unique([
                'ref_cod_calendario_ano_letivo',
                'mes',
                'dia',
            ]);
        });

        DB::unprepared('ALTER TABLE IF EXISTS pmieducar.calendario_dia_anotacao ADD CONSTRAINT calendario_dia_anotacao_ref_ref_cod_calendario_ano_letivo_fkey FOREIGN KEY (ref_ref_cod_calendario_ano_letivo, ref_dia, ref_mes) REFERENCES pmieducar.calendario_dia (ref_cod_calendario_ano_letivo, dia, mes) MATCH SIMPLE ON UPDATE RESTRICT ON DELETE RESTRICT;');
        DB::unprepared('ALTER TABLE IF EXISTS modules.calendario_turma ADD CONSTRAINT calendario_turma_calendario_dia_fk FOREIGN KEY (mes, dia, calendario_ano_letivo_id) REFERENCES pmieducar.calendario_dia (mes, dia, ref_cod_calendario_ano_letivo) MATCH SIMPLE ON UPDATE NO ACTION ON DELETE CASCADE');
    }

    public function down()
    {
        DB::unprepared('ALTER TABLE IF EXISTS modules.calendario_turma DROP CONSTRAINT IF EXISTS calendario_turma_calendario_dia_fk;');
        DB::unprepared('ALTER TABLE IF EXISTS pmieducar.calendario_dia_anotacao DROP CONSTRAINT IF EXISTS calendario_dia_anotacao_ref_ref_cod_calendario_ano_letivo_fkey;');

        DB::unprepared('ALTER TABLE IF EXISTS pmieducar.calendario_dia DROP CONSTRAINT IF EXISTS pmieducar_calendario_dia_ref_cod_calendario_ano_letivo_foreign;');
        DB::unprepared('ALTER TABLE IF EXISTS pmieducar.calendario_dia DROP CONSTRAINT IF EXISTS pmieducar_calendario_dia_ref_cod_calendario_dia_motivo_foreign;');
        DB::unprepared('ALTER TABLE IF EXISTS pmieducar.calendario_dia DROP CONSTRAINT IF EXISTS pmieducar_calendario_dia_ref_usuario_cad_foreign;');
        DB::unprepared('ALTER TABLE IF EXISTS pmieducar.calendario_dia DROP CONSTRAINT IF EXISTS pmieducar_calendario_dia_ref_usuario_exc_foreign;');

        DB::unprepared('ALTER TABLE IF EXISTS pmieducar.calendario_dia DROP CONSTRAINT IF EXISTS pmieducar_calendario_dia_ref_cod_calendario_ano_letivo_mes_dia_;');

        Schema::table('pmieducar.calendario_dia', function (Blueprint $table) {
            $table->dropColumn('id');
        });

        DB::unprepared('ALTER TABLE IF EXISTS pmieducar.calendario_dia ADD CONSTRAINT calendario_dia_ref_cod_calendario_ano_letivo_fkey FOREIGN KEY (ref_cod_calendario_ano_letivo) REFERENCES pmieducar.calendario_ano_letivo (cod_calendario_ano_letivo) MATCH SIMPLE ON UPDATE RESTRICT ON DELETE RESTRICT');
        DB::unprepared('ALTER TABLE IF EXISTS pmieducar.calendario_dia ADD CONSTRAINT calendario_dia_ref_cod_calendario_dia_motivo_fkey FOREIGN KEY (ref_cod_calendario_dia_motivo) REFERENCES pmieducar.calendario_dia_motivo (cod_calendario_dia_motivo) MATCH SIMPLE ON UPDATE RESTRICT ON DELETE RESTRICT');
        DB::unprepared('ALTER TABLE IF EXISTS pmieducar.calendario_dia ADD CONSTRAINT calendario_dia_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario (cod_usuario) MATCH SIMPLE ON UPDATE RESTRICT ON DELETE RESTRICT');
        DB::unprepared('ALTER TABLE IF EXISTS pmieducar.calendario_dia ADD CONSTRAINT calendario_dia_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario (cod_usuario) MATCH SIMPLE ON UPDATE RESTRICT ON DELETE RESTRICT');
    }
};
