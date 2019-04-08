<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AdicionaColunaTurnoMatriculaTurmaExcluidos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pmieducar.matricula_turma_excluidos', function (Blueprint $table) {
            $table->integer('turno_id')->nullable();
        });

        $createFunction = <<<'SQL'
                CREATE OR REPLACE FUNCTION pmieducar.delete_matricula_turma() RETURNS TRIGGER
                  LANGUAGE plpgsql
                AS
                $$
                BEGIN
                  INSERT INTO pmieducar.matricula_turma_excluidos
                  (id,
                   ref_cod_matricula,
                   ref_cod_turma,
                   sequencial,
                   ref_usuario_exc,
                   ref_usuario_cad,
                   data_cadastro,
                   data_exclusao,
                   ativo,
                   data_enturmacao,
                   sequencial_fechamento,
                   transferido,
                   remanejado,
                   reclassificado,
                   abandono,
                   updated_at,
                   falecido,
                   etapa_educacenso,
                   turma_unificada,
                   turno_id,
                   deleted_at)
                  VALUES (OLD.id,
                          OLD.ref_cod_matricula,
                          OLD.ref_cod_turma,
                          OLD.sequencial,
                          OLD.ref_usuario_exc,
                          OLD.ref_usuario_cad,
                          OLD.data_cadastro,
                          OLD.data_exclusao,
                          OLD.ativo,
                          OLD.data_enturmacao,
                          OLD.sequencial_fechamento,
                          OLD.transferido,
                          OLD.remanejado,
                          OLD.reclassificado,
                          OLD.abandono,
                          OLD.updated_at,
                          OLD.falecido,
                          OLD.etapa_educacenso,
                          OLD.turma_unificada,
                          OLD.turno_id,
                          NOW());
                  RETURN OLD;
                END;
                $$;
SQL;

        DB::statement($createFunction);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pmieducar.matricula_turma_excluidos', function (Blueprint $table) {
            $table->dropColumn('turno_id');
        });
    }
}
