<?php

namespace Tests\Feature\DiarioApi;

use App\Models\LegacyEnrollment;
use Illuminate\Foundation\Testing\TestResponse;

trait DiarioApiTestTrait
{
    /**
     * @param LegacyEnrollment $enrollment
     * @param $disciplineId
     * @param $stage
     * @param $score
     * @return TestResponse
     */
    public function postScore($enrollment, $disciplineId, $stage, $score)
    {
        $schoolClass = $enrollment->schoolClass;

        $data = [
            'resource' => 'falta',
            'oper' => 'post',
            'instituicao_id' => $schoolClass->school->institution->id,
            'escola_id' => $schoolClass->school_id,
            'curso_id' => $schoolClass->course_id,
            'serie_id' => $schoolClass->grade_id,
            'turma_id' => $schoolClass->id,
            'ano_escolar' => $schoolClass->year,
            'componente_curricular_id' => $disciplineId,
            'etapa' => $stage,
            'matricula_id' => $enrollment->registration->id,
            'att_value' => $score,
            'access_key' => env('API_ACCESS_KEY'),
            'secret_key' => env('API_SECRET_KEY'),
        ];

        $response = $this->get(env('APP_URL') . '/module/Avaliacao/diarioApi?' . http_build_query($data));

        return $response;
    }
}
