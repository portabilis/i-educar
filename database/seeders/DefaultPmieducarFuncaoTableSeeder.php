<?php

namespace Database\Seeders;

use App\Models\LegacyRole;
use App\Models\LegacyUser;
use Illuminate\Database\Seeder;

class DefaultPmieducarFuncaoTableSeeder extends Seeder
{
    public function run()
    {
        $user = LegacyUser::query()
            ->orderBy('cod_usuario')
            ->first();

        $roles = [
            'CP' => 'Coordenador(a) Pedagógico',
            'DIR' => 'Diretor(a) Escolar',
            'SEC' => 'Secretário(a) Escolar',
            'SERV' => 'Servente Escolar',
            'ZEL' => 'Zelador(a)',
            'OE' => 'Orientador(a) Educacional',
            'AUX' => 'Auxiliar Administrativo',
            'BIB' => 'Bibliotecário(a)',
            'TEC-INF' => 'Técnico de Informática / Monitor de Laboratório',
            'ASG' => 'Auxiliar de Serviços Gerais',
        ];

        foreach ($roles as $sg => $role) {
            LegacyRole::updateOrCreate([
                'abreviatura' => $sg,
            ], [
                'nm_funcao' => $role,
                'professor' => 0,
                'ativo' => 1,
                'ref_usuario_cad' => $user?->getKey(),
                'ref_cod_instituicao' => 1,
            ]);
        }

        $teachers = [
            'PROF' => 'Professor(a)',
            'PROF-AEE' => 'Professor(a) de AEE',
        ];

        foreach ($teachers as $sg => $teacher) {
            LegacyRole::updateOrCreate([
                'abreviatura' => $sg,
            ], [
                'nm_funcao' => $teacher,
                'professor' => 1,
                'ativo' => 1,
                'ref_usuario_cad' => $user?->getKey(),
                'ref_cod_instituicao' => 1,
            ]);
        }
    }
}
