<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class DropConstraints extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('ALTER TABLE pmieducar.historico_disciplinas DROP CONSTRAINT IF EXISTS pmieducar_historico_disciplinas_sequencial_ref_ref_cod_aluno_re;');
        DB::unprepared('ALTER TABLE pmieducar.historico_escolar DROP CONSTRAINT IF EXISTS pmieducar_historico_escolar_ref_cod_aluno_sequencial_unique;');
        DB::unprepared('ALTER TABLE pmieducar.menu_tipo_usuario ADD CONSTRAINT menu_tipo_usuario_pkey PRIMARY KEY (ref_cod_tipo_usuario, menu_id);');
        DB::unprepared('CREATE UNIQUE INDEX pmieducar_historico_disciplinas_sequencial_ref_ref_cod_aluno_re ON pmieducar.historico_disciplinas USING btree (sequencial, ref_ref_cod_aluno, ref_sequencial);');
        DB::unprepared('CREATE UNIQUE INDEX pmieducar_historico_escolar_ref_cod_aluno_sequencial_unique ON pmieducar.historico_escolar USING btree (ref_cod_aluno, sequencial);');
    }
}
