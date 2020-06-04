<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DefaultModulesTipoVeiculoTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('modules.tipo_veiculo')->insert(['cod_tipo_veiculo' => 1, 'descricao' => 'Vans/Kombis']);
        DB::table('modules.tipo_veiculo')->insert(['cod_tipo_veiculo' => 2, 'descricao' => 'Microônibus']);
        DB::table('modules.tipo_veiculo')->insert(['cod_tipo_veiculo' => 3, 'descricao' => 'Ônibus']);
        DB::table('modules.tipo_veiculo')->insert(['cod_tipo_veiculo' => 4, 'descricao' => 'Bicicleta']);
        DB::table('modules.tipo_veiculo')->insert(['cod_tipo_veiculo' => 5, 'descricao' => 'Tração Animal']);
        DB::table('modules.tipo_veiculo')->insert(['cod_tipo_veiculo' => 6, 'descricao' => 'Outro']);
        DB::table('modules.tipo_veiculo')->insert(['cod_tipo_veiculo' => 7, 'descricao' => 'Capacidade de até 5 Alunos']);
        DB::table('modules.tipo_veiculo')->insert(['cod_tipo_veiculo' => 8, 'descricao' => 'Capacidade entre 5 a 15 Alunos']);
        DB::table('modules.tipo_veiculo')->insert(['cod_tipo_veiculo' => 9, 'descricao' => 'Capacidade entre 15 a 35 Alunos']);
        DB::table('modules.tipo_veiculo')->insert(['cod_tipo_veiculo' => 10, 'descricao' => 'Capacidade acima de 35 Alunos']);
        DB::table('modules.tipo_veiculo')->insert(['cod_tipo_veiculo' => 11, 'descricao' => 'Trem/Metrô']);
    }
}
