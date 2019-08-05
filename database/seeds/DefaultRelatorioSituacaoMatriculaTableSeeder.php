<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DefaultRelatorioSituacaoMatriculaTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('relatorio.situacao_matricula')->insert(['cod_situacao' => 1, 'descricao' => 'Aprovado']);
        DB::table('relatorio.situacao_matricula')->insert(['cod_situacao' => 2, 'descricao' => 'Reprovado']);
        DB::table('relatorio.situacao_matricula')->insert(['cod_situacao' => 15, 'descricao' => 'Falecido']);
        DB::table('relatorio.situacao_matricula')->insert(['cod_situacao' => 4, 'descricao' => 'Transferido']);
        DB::table('relatorio.situacao_matricula')->insert(['cod_situacao' => 6, 'descricao' => 'Abandono']);
        DB::table('relatorio.situacao_matricula')->insert(['cod_situacao' => 13, 'descricao' => 'Aprovado pelo conselho']);
        DB::table('relatorio.situacao_matricula')->insert(['cod_situacao' => 9, 'descricao' => 'Exceto Transferidos/Abandono']);
        DB::table('relatorio.situacao_matricula')->insert(['cod_situacao' => 10, 'descricao' => 'Todas']);
        DB::table('relatorio.situacao_matricula')->insert(['cod_situacao' => 14, 'descricao' => 'Reprovado por faltas']);
        DB::table('relatorio.situacao_matricula')->insert(['cod_situacao' => 12, 'descricao' => 'Ap. Depen.']);
        DB::table('relatorio.situacao_matricula')->insert(['cod_situacao' => 5, 'descricao' => 'Reclassificado']);
        DB::table('relatorio.situacao_matricula')->insert(['cod_situacao' => 3, 'descricao' => 'Cursando']);
    }
}
