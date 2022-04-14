<?php

namespace Tests\Feature\DiarioApi;

use App\Models\LegacyDiscipline;
use App\Models\LegacyEnrollment;
use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\TestResponse;

trait DiarioApiRequestTestTrait
{
    /**
     * @param LegacyEnrollment $enrollment
     * @param                  $disciplineId
     * @param                  $stage
     * @param                  $score
     *
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

        $user = UserFactory::new()->admin()->make();

        /** @var TestResponse $response */
        $response = $this->actingAs($user)->get('/module/Avaliacao/diarioApi?' . http_build_query($data));

        return json_decode($response->content());
    }

    public function postExam($enrollment, $disciplineId, $stage, $score)
    {
        return $this->postScore($enrollment, $disciplineId, $stage, $score, 'nota_exame');
    }

    public function postScore($enrollment, $disciplineId, $stage, $score, $resource = 'nota')
    {
        $this->cleanGlobals();

        $schoolClass = $enrollment->schoolClass;

        $data = [
            'resource' => $resource,
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

        $user = UserFactory::new()->admin()->make();

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

        $user = UserFactory::new()->admin()->make();

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

        $user = UserFactory::new()->admin()->make();

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

    /**
     * Recebe um array com as etapas e quantidade de faltas e retorna o response da ultima chamada
     *
     * @param array            $absences
     * @param LegacyDiscipline $discipline
     *
     * @return array|null
     */
    private function postAbsenceForStages(array $absences, $discipline)
    {
        $response = null;

        foreach ($absences as $stage => $absence) {
            $response = $this->postAbsence($this->enrollment, $discipline->id, $stage, $absence);
        }

        return $response;
    }

    /**
     * Recebe um array com as etapas e quantidade de notas e retorna o response da ultima chamada
     *
     * @param array            $scores
     * @param LegacyDiscipline $discipline
     *
     * @return array|null
     */
    private function postScoreForStages(array $scores, $discipline)
    {
        $response = null;

        foreach ($scores as $stage => $score) {
            $response = $this->postScore($this->enrollment, $discipline->id, $stage, $score);
        }

        return $response;
    }
}
