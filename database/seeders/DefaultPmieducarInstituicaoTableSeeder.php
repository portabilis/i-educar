<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DefaultPmieducarInstituicaoTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('pmieducar.instituicao')->insert([
            'cod_instituicao' => 1,
            'ref_usuario_cad' => 1,
            'ref_idtlog' => 'RUA',
            'ref_sigla_uf' => 'SC',
            'cep' => 88820000,
            'cidade' => 'Modelópolis',
            'bairro' => 'Centro',
            'logradouro' => 'Rua João Paulo Segundo',
            'nm_responsavel' => 'Secretaria de Educação e Cultura',
            'data_cadastro' => now(),
            'nm_instituicao' => 'Prefeitura Municipal de Modelópolis',
        ]);
    }
}
