<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up()
    {
        DB::unprepared('ALTER TABLE IF EXISTS pmieducar.avaliacao_desempenho DROP CONSTRAINT IF EXISTS avaliacao_desempenho_pkey;');
        DB::unprepared('ALTER TABLE IF EXISTS pmieducar.avaliacao_desempenho DROP CONSTRAINT IF EXISTS pmieducar_avaliacao_desempenho_sequential_employee_id_instituti;');
        DB::unprepared('ALTER TABLE IF EXISTS pmieducar.avaliacao_desempenho DROP CONSTRAINT IF EXISTS avaliacao_desempenho_ref_cod_servidor_fkey;');
        DB::unprepared('ALTER TABLE IF EXISTS pmieducar.avaliacao_desempenho DROP CONSTRAINT IF EXISTS avaliacao_desempenho_ref_usuario_cad_fkey;');
        DB::unprepared('ALTER TABLE IF EXISTS pmieducar.avaliacao_desempenho DROP CONSTRAINT IF EXISTS avaliacao_desempenho_ref_usuario_exc_fkey;');
        DB::unprepared('ALTER TABLE IF EXISTS pmieducar.avaliacao_desempenho RENAME sequencial TO sequential;');
        DB::unprepared('ALTER TABLE IF EXISTS pmieducar.avaliacao_desempenho RENAME ref_cod_servidor TO employee_id;');
        DB::unprepared('ALTER TABLE IF EXISTS pmieducar.avaliacao_desempenho RENAME ref_ref_cod_instituicao TO institution_id;');
        DB::unprepared('ALTER TABLE IF EXISTS pmieducar.avaliacao_desempenho RENAME ref_usuario_exc TO deleted_by;');
        DB::unprepared('ALTER TABLE IF EXISTS pmieducar.avaliacao_desempenho RENAME ref_usuario_cad TO created_by;');
        DB::unprepared('ALTER TABLE IF EXISTS pmieducar.avaliacao_desempenho RENAME descricao TO description;');
        DB::unprepared('ALTER TABLE IF EXISTS pmieducar.avaliacao_desempenho RENAME data_cadastro TO created_at;');
        DB::unprepared('ALTER TABLE IF EXISTS pmieducar.avaliacao_desempenho RENAME data_exclusao TO deleted_at;');
        DB::unprepared('ALTER TABLE IF EXISTS pmieducar.avaliacao_desempenho RENAME titulo_avaliacao TO title;');
        DB::unprepared('UPDATE pmieducar.avaliacao_desempenho SET deleted_at = now() WHERE ativo = 0;');
        DB::unprepared('ALTER TABLE IF EXISTS pmieducar.avaliacao_desempenho DROP COLUMN ativo;');

        Schema::table('pmieducar.avaliacao_desempenho', function (
            Blueprint $table
        ) {
            $table->increments('id');
            $table->timestamp('updated_at')->nullable();
            $table->unique([
                'sequential',
                'employee_id',
                'institution_id',
            ]);
            $table->foreign(['employee_id', 'institution_id'])
                ->references(['cod_servidor', 'ref_cod_instituicao'])
                ->on('pmieducar.servidor')
                ->onUpdate('restrict')
                ->onDelete('restrict');
            $table->foreign('created_by')
                ->references('cod_usuario')
                ->on('pmieducar.usuario')
                ->onUpdate('restrict')
                ->onDelete('restrict');
            $table->foreign('deleted_by')
                ->references('cod_usuario')
                ->on('pmieducar.usuario')
                ->onUpdate('restrict')
                ->onDelete('restrict');
        });
    }

    public function down()
    {
        DB::unprepared('ALTER TABLE IF EXISTS pmieducar.avaliacao_desempenho DROP CONSTRAINT IF EXISTS pmieducar_avaliacao_desempenho_created_by_foreign;');
        DB::unprepared('ALTER TABLE IF EXISTS pmieducar.avaliacao_desempenho DROP CONSTRAINT IF EXISTS pmieducar_avaliacao_desempenho_deleted_by_foreign;');
        DB::unprepared('ALTER TABLE IF EXISTS pmieducar.avaliacao_desempenho DROP CONSTRAINT IF EXISTS pmieducar_avaliacao_desempenho_employee_id_institution_id_forei;');
        DB::unprepared('ALTER TABLE IF EXISTS pmieducar.avaliacao_desempenho DROP CONSTRAINT IF EXISTS pmieducar_avaliacao_desempenho_ref_cod_servidor_ref_ref_cod_ins;');
        DB::unprepared('ALTER TABLE IF EXISTS pmieducar.avaliacao_desempenho RENAME sequential TO sequencial;');
        DB::unprepared('ALTER TABLE IF EXISTS pmieducar.avaliacao_desempenho RENAME employee_id TO ref_cod_servidor;');
        DB::unprepared('ALTER TABLE IF EXISTS pmieducar.avaliacao_desempenho RENAME institution_id TO ref_ref_cod_instituicao;');
        DB::unprepared('ALTER TABLE IF EXISTS pmieducar.avaliacao_desempenho RENAME deleted_by TO ref_usuario_exc;');
        DB::unprepared('ALTER TABLE IF EXISTS pmieducar.avaliacao_desempenho RENAME created_by TO ref_usuario_cad;');
        DB::unprepared('ALTER TABLE IF EXISTS pmieducar.avaliacao_desempenho RENAME description TO descricao;');
        DB::unprepared('ALTER TABLE IF EXISTS pmieducar.avaliacao_desempenho RENAME created_at TO data_cadastro;');
        DB::unprepared('ALTER TABLE IF EXISTS pmieducar.avaliacao_desempenho RENAME deleted_at TO data_exclusao;');
        DB::unprepared('ALTER TABLE IF EXISTS pmieducar.avaliacao_desempenho RENAME title TO titulo_avaliacao;');
        DB::unprepared('ALTER TABLE IF EXISTS pmieducar.avaliacao_desempenho ADD COLUMN ativo smallint NOT NULL DEFAULT (1)::smallint;');
        DB::unprepared('UPDATE pmieducar.avaliacao_desempenho SET ativo = 0 WHERE data_exclusao IS NOT NULL;');

        DB::unprepared('ALTER TABLE IF EXISTS pmieducar.avaliacao_desempenho DROP CONSTRAINT IF EXISTS avaliacao_desempenho_pkey;');
        DB::unprepared('ALTER TABLE IF EXISTS pmieducar.avaliacao_desempenho DROP CONSTRAINT IF EXISTS performance_evaluations_sequential_employee_id_institution_id_u;');
        Schema::table('pmieducar.avaliacao_desempenho', function (
            Blueprint $table
        ) {
            $table->dropColumn('id');
            $table->dropColumn('updated_at');
        });
        DB::unprepared('ALTER TABLE IF EXISTS pmieducar.avaliacao_desempenho ADD CONSTRAINT avaliacao_desempenho_pkey PRIMARY KEY (sequencial, ref_cod_servidor, ref_ref_cod_instituicao);');
    }
};
