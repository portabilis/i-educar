<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class RemoveAuditTriggers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared(
            file_get_contents(__DIR__ . '/../sqls/triggers/remove-audit-triggers.sql')
        );

        DB::unprepared(<<<'SQL'
                DROP FUNCTION IF EXISTS modules.audita_falta_componente_curricular();
                DROP FUNCTION IF EXISTS modules.audita_falta_geral();
                DROP FUNCTION IF EXISTS modules.audita_media_geral();
                DROP FUNCTION IF EXISTS modules.audita_nota_componente_curricular();
                DROP FUNCTION IF EXISTS modules.audita_nota_componente_curricular_media();
                DROP FUNCTION IF EXISTS modules.audita_nota_exame();
                DROP FUNCTION IF EXISTS modules.audita_nota_geral();
                DROP FUNCTION IF EXISTS modules.audita_parecer_componente_curricular();
                DROP FUNCTION IF EXISTS modules.audita_parecer_geral();
                DROP FUNCTION IF EXISTS pmieducar.audita_matricula();
                DROP FUNCTION IF EXISTS pmieducar.audita_matricula_turma();
SQL);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared(
            file_get_contents(__DIR__ . '/../sqls/triggers/add-audit-triggers.sql')
        );

        DB::unprepared(file_get_contents(__DIR__ . '/../sqls/functions/modules.audita_falta_componente_curricular.sql'));
        DB::unprepared(file_get_contents(__DIR__ . '/../sqls/functions/modules.audita_falta_geral.sql'));
        DB::unprepared(file_get_contents(__DIR__ . '/../sqls/functions/modules.audita_media_geral.sql'));
        DB::unprepared(file_get_contents(__DIR__ . '/../sqls/functions/modules.audita_nota_componente_curricular.sql'));
        DB::unprepared(file_get_contents(__DIR__ . '/../sqls/functions/modules.audita_nota_componente_curricular_media.sql'));
        DB::unprepared(file_get_contents(__DIR__ . '/../sqls/functions/modules.audita_nota_exame.sql'));
        DB::unprepared(file_get_contents(__DIR__ . '/../sqls/functions/modules.audita_nota_geral.sql'));
        DB::unprepared(file_get_contents(__DIR__ . '/../sqls/functions/modules.audita_parecer_componente_curricular.sql'));
        DB::unprepared(file_get_contents(__DIR__ . '/../sqls/functions/modules.audita_parecer_geral.sql'));
        DB::unprepared(file_get_contents(__DIR__ . '/../sqls/functions/pmieducar.audita_matricula.sql'));
        DB::unprepared(file_get_contents(__DIR__ . '/../sqls/functions/pmieducar.audita_matricula_turma.sql'));
    }
}
