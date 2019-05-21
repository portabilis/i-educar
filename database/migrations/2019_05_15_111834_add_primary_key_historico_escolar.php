<?php

use App\Support\Database\PrimaryKey;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddPrimaryKeyHistoricoEscolar extends Migration
{
    use PrimaryKey;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->removeForeignKeys();

        $this->createPrimaryKey('pmieducar.historico_escolar');

        $this->createForeignKeys();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->removePrimaryKey('pmieducar.historico_escolar', ['ref_cod_aluno', 'sequencial']);
    }

    private function removeForeignKeys()
    {
        $this->dropForeignKey('pmieducar.historico_disciplinas', 'historico_disciplinas_ref_ref_cod_aluno_fkey');
    }

    private function createForeignKeys()
    {
        DB::statement("alter table pmieducar.historico_disciplinas
	        add constraint historico_disciplinas_ref_ref_cod_aluno_fkey
		    foreign key (ref_ref_cod_aluno, ref_sequencial) references pmieducar.historico_escolar (ref_cod_aluno, sequencial)
			on update cascade on delete cascade;");
    }
}
