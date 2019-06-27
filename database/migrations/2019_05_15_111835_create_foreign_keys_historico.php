<?php

use Illuminate\Support\Facades\DB;

use Illuminate\Database\Migrations\Migration;

class CreateForeignKeysHistorico extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        try {
            DB::statement("alter table pmieducar.historico_disciplinas
	        add constraint historico_disciplinas_ref_ref_cod_aluno_fkey
		    foreign key (ref_ref_cod_aluno, ref_sequencial) references pmieducar.historico_escolar (ref_cod_aluno, sequencial)
			on update cascade on delete cascade;");
        } catch (\Throwable $exception) {
            // Para o caso de algumas bases com inconsistência no histórico
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

    }
}
