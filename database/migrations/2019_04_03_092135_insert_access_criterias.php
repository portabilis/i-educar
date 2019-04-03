<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class InsertAccessCriterias extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('manager_access_criterias')->insert(
            [
                [
                    'id' => 1,
                    'name' => 'Proprietário(a) ou sócio(a)-proprietário(a) da escola'
                ],
                [
                    'id' => 2,
                    'name' => 'Exclusivamente por indicação/escolha da gestão'
                ],
                [
                    'id' => 3,
                    'name' => 'Processo seletivo qualificado e escolha/nomeação da gestão'
                ],
                [
                    'id' => 4,
                    'name' => 'Concurso público específico para o cargo de gestor escolar'
                ],
                [
                    'id' => 5,
                    'name' => 'Exclusivamente por processo eleitoral com a participação da comunidade escolar'
                ],
                [
                    'id' => 6,
                    'name' => 'Processo seletivo qualificado e eleição com a participação da comunidade escolar'
                ],
                [
                    'id' => 7,
                    'name' => 'Outros'
                ],
            ]
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
