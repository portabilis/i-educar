<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DefaultPmieducarTurmaTurnoTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('pmieducar.turma_turno')->insert(['id' => 1, 'nome' => 'Matutino']);
        DB::table('pmieducar.turma_turno')->insert(['id' => 2, 'nome' => 'Vespertino']);
        DB::table('pmieducar.turma_turno')->insert(['id' => 3, 'nome' => 'Noturno']);
        DB::table('pmieducar.turma_turno')->insert(['id' => 4, 'nome' => 'Integral']);
    }
}
