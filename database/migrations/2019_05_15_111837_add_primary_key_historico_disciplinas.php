<?php

use App\Support\Database\PrimaryKey;
use Illuminate\Database\Migrations\Migration;

class AddPrimaryKeyHistoricoDisciplinas extends Migration
{
    use PrimaryKey;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->createPrimaryKey('pmieducar.historico_disciplinas');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->removePrimaryKey('pmieducar.historico_disciplinas', ['ref_ref_cod_aluno', 'sequencial', 'ref_sequencial']);
    }
}
