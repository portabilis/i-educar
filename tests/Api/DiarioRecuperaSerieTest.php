<?php

namespace Tests\Api;

use Database\Factories\LegacyCourseFactory;
use Database\Factories\LegacyGradeFactory;
use Database\Factories\LegacySchoolFactory;
use Database\Factories\LegacySchoolGradeFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class DiarioRecuperaSerieTest extends TestCase
{
    use DatabaseTransactions;
    use DiarioApiRequestTestTrait;

    public function testRecuperaCurso()
    {
        $school = LegacySchoolFactory::new()->create();

        $course = LegacyCourseFactory::new()->standardAcademicYear()->create();

        $level = LegacyGradeFactory::new()->create([
            'ref_cod_curso' => $course,
            'dias_letivos' => '200',
        ]);

        LegacySchoolGradeFactory::new()->create([
            'ref_cod_serie' => $level,
            'ref_cod_escola' => $school,
        ]);

        $data = [
            'oper' => 'get',
            'resource' => 'series',
            'instituicao_id' => $course->ref_cod_instituicao,
        ];

        $response = $this->getResource('/module/Api/Serie', $data);

        $response->assertSuccessful()
            ->assertJsonCount(1, 'series')
            ->assertJson(
                [
                    'series' => [
                        0 => [
                            'id' => $level->getKey(),
                            'nome' => mb_strtoupper($level->name),
                            'idade_padrao' => $level->idade_ideal,
                            'curso_id' => $level->course_id,
                            'deleted_at' => null,
                        ],
                    ],
                    'oper' => 'get',
                    'resource' => 'series',
                    'msgs' => [],
                    'any_error_msg' => false,
                ]
            )->assertJsonStructure(
                [
                    'series' => [
                        [
                            'updated_at',
                        ],
                    ],
                ]
            );
    }
}
