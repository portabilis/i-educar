<?php

namespace Database\Seeders;

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
            'nm_vinculo' => 'Efetivo',
            'abreviatura' => 'Efet',
        ]);

        DB::table('portal.funcionario_vinculo')->insert([
            'cod_funcionario_vinculo' => 4,
            'nm_vinculo' => 'Contratado',
            'abreviatura' => 'Cont',
        ]);

        DB::table('portal.funcionario_vinculo')->insert([
            'cod_funcionario_vinculo' => 5,
            'nm_vinculo' => 'Comissionado',
            'abreviatura' => 'Com',
        ]);

        DB::table('portal.funcionario_vinculo')->insert([
            'cod_funcionario_vinculo' => 6,
            'nm_vinculo' => 'Estagiário',
            'abreviatura' => 'Est',
        ]);
    }
}
