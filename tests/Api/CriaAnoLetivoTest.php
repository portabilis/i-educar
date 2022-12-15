<?php

namespace Tests\Api;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\LoginFirstUser;
use Tests\TestCase;

class CriaAnoLetivoTest extends TestCase
{
    use DatabaseTransactions, LoginFirstUser;

    public function testCreateNewSchoolAcademicYear()
    {
        // setup

        $request = [
            'tipoacao' => 'Novo',
            'ref_ano' => 2023 + 1,
            'ref_ref_cod_escola' => 13,
            'ref_cod_modulo' => 5,
            'data_inicio[0]' => '01/01/2023',
            'data_fim[0]' => '10/10/2023',
            'dias_letivos[0]' => 100
        ];
    }
}
