<?php

namespace Tests\Feature\DiarioApi;

use App\Models\LegacyEnrollment;
use App\User;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Http\Request;

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
        $this->cleanGlobals();

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

        $_GET = $data;

        $user = factory(User::class, 'admin')->make();

        /** @var TestResponse $response */
        $response = $this->actingAs($user)->get('/module/Avaliacao/diarioApi?' . http_build_query($data));

        return json_decode($response->content());
    }

    public function postScore($enrollment, $disciplineId, $stage, $score)
    {
        $this->cleanGlobals();

        $schoolClass = $enrollment->schoolClass;

        $data = [
            'resource' => 'nota',
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

        $_GET = $data;

        $user = factory(User::class, 'admin')->make();

        /** @var TestResponse $response */
        $response = $this->actingAs($user)->get('/module/Avaliacao/diarioApi?' . http_build_query($data));

        return json_decode($response->content());
    }

    public function deleteScore($enrollment, $disciplineId, $stage)
    {
        $this->cleanGlobals();

        $schoolClass = $enrollment->schoolClass;

        $data = [
            'resource' => 'nota',
            'oper' => 'delete',
            'instituicao_id' => $schoolClass->school->institution->id,
            'escola_id' => $schoolClass->school_id,
            'curso_id' => $schoolClass->course_id,
            'serie_id' => $schoolClass->grade_id,
            'turma_id' => $schoolClass->id,
            'ano_escolar' => $schoolClass->year,
            'componente_curricular_id' => $disciplineId,
            'etapa' => $stage,
            'matricula_id' => $enrollment->registration->id,
            'access_key' => env('API_ACCESS_KEY'),
            'secret_key' => env('API_SECRET_KEY'),
        ];

        $_GET = $data;

        $user = factory(User::class, 'admin')->make();

        /** @var TestResponse $response */
        $response = $this->actingAs($user)->get('/module/Avaliacao/diarioApi?' . http_build_query($data));

        return json_decode($response->content());
    }

    public function deleteAbsence($enrollment, $disciplineId, $stage)
    {
        $this->cleanGlobals();

        $schoolClass = $enrollment->schoolClass;

        $data = [
            'resource' => 'falta',
            'oper' => 'delete',
            'instituicao_id' => $schoolClass->school->institution->id,
            'escola_id' => $schoolClass->school_id,
            'curso_id' => $schoolClass->course_id,
            'serie_id' => $schoolClass->grade_id,
            'turma_id' => $schoolClass->id,
            'ano_escolar' => $schoolClass->year,
            'componente_curricular_id' => $disciplineId,
            'etapa' => $stage,
            'matricula_id' => $enrollment->registration->id,
            'access_key' => env('API_ACCESS_KEY'),
            'secret_key' => env('API_SECRET_KEY'),
        ];

        $_GET = $data;

        $user = factory(User::class, 'admin')->make();

        /** @var TestResponse $response */
        $response = $this->actingAs($user)->get('/module/Avaliacao/diarioApi?' . http_build_query($data));

        return json_decode($response->content());
    }

    /**
     * Limpa variáveis globais que podem interferir testes no código legado
     */
    public function cleanGlobals()
    {
        $_GET = null;
        $_POST = null;
    }
}
