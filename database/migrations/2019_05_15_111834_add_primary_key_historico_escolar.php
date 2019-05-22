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
        $this->createPrimaryKey('pmieducar.historico_escolar');
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
}
