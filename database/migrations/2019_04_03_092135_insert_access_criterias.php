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
        DB::table('manager_access_criterias')->updateOrInsert(
            ['id' => 1],
            ['name' => 'Proprietário(a) ou sócio(a)-proprietário(a) da escola']
        );

        DB::table('manager_access_criterias')->updateOrInsert(
            ['id' => 2],
            ['name' => 'Exclusivamente por indicação/escolha da gestão']
        );

        DB::table('manager_access_criterias')->updateOrInsert(
            ['id' => 3],
            ['name' => 'Processo seletivo qualificado e escolha/nomeação da gestão']
        );

        DB::table('manager_access_criterias')->updateOrInsert(
            ['id' => 4],
            ['name' => 'Concurso público específico para o cargo de gestor escolar']
        );

        DB::table('manager_access_criterias')->updateOrInsert(
            ['id' => 4],
            ['name' => 'Concurso público específico para o cargo de gestor escolar']
        );

        DB::table('manager_access_criterias')->updateOrInsert(
            ['id' => 5],
            ['name' => 'Exclusivamente por processo eleitoral com a participação da comunidade escolar']
        );

        DB::table('manager_access_criterias')->updateOrInsert(
            ['id' => 6],
            ['name' => 'Processo seletivo qualificado e eleição com a participação da comunidade escolar']
        );

        DB::table('manager_access_criterias')->updateOrInsert(
            ['id' => 7],
            ['name' => 'Outros']
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
