<?php

use App\Support\Database\AsView;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class AlteraColunaVeiculoTransporteEscolarAluno extends Migration
{
    use AsView;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('pmieducar.aluno')
            ->where('veiculo_transporte_escolar', 11) //Ferroviário - Trem/Metrô
            ->update(['veiculo_transporte_escolar' => null]);

        $this->dropView('students');
        DB::statement('alter table pmieducar.aluno alter column veiculo_transporte_escolar type integer[] USING array[veiculo_transporte_escolar]::integer[]');
        $this->createView('students');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('alter table pmieducar.aluno alter column veiculo_transporte_escolar type smallint USING veiculo_transporte_escolar[1]::integer');
    }
}
