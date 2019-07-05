<?php

namespace Tests\Feature\DiarioApi;

use App\Models\LegacyEnrollment;
use App\User;
use Illuminate\Foundation\Testing\TestResponse;

trait DiarioApiRequestTestTrait
{
    /**
     * @param LegacyEnrollment $enrollment
     * @param $disciplineId
     * @param $stage
     * @param $score
     * @return array
     */
    public function postAbsence($enrollment, $disciplineId, $stage, $score)
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

        $user = factory(User::class, 'admin')->make();

        /** @var TestResponse $response */
        $response = $this->actingAs($user)->get(env('APP_URL') . '/module/Avaliacao/diarioApi?' . http_build_query($data));

        return json_decode($response->content());
    }
}
