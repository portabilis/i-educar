<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DefaultPortalFuncionarioVinculoTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('portal.funcionario_vinculo')->insert([
            'cod_funcionario_vinculo' => 3,
            'nome' => 'Efetivo',
            'abreviatura' => 'Efet',
        ]);

        DB::table('portal.funcionario_vinculo')->insert([
            'cod_funcionario_vinculo' => 4,
            'nome' => 'Contratado',
            'abreviatura' => 'Cont',
        ]);

        DB::table('portal.funcionario_vinculo')->insert([
            'cod_funcionario_vinculo' => 5,
            'nome' => 'Comissionado',
            'abreviatura' => 'Com',
        ]);

        DB::table('portal.funcionario_vinculo')->insert([
            'cod_funcionario_vinculo' => 6,
            'nome' => 'Estagiário',
            'abreviatura' => 'Est',
        ]);
    }
}
