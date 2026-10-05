<?php

namespace Tests\Api;

use App\User;
use Database\Factories\LegacyGradeFactory;
use Database\Factories\LegacyRegistrationFactory;
use Database\Factories\LegacySchoolClassFactory;
use Database\Factories\LegacyUserFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;

class ProcessamentoHistoricoMatriculasTest extends TestCase
{
    use DatabaseTransactions, WithoutMiddleware;

    private function callApi(string $method, string $path, array $params, User $user)
    {
        $_GET = $params;

        return $this->actingAs($user)->call($method, $path . '?' . http_build_query($params));
    }

    public function test_lista_de_matriculas_traz_a_serie_de_cada_matricula()
    {
        $schoolClass = LegacySchoolClassFactory::new()->create();
        $registration = LegacyRegistrationFactory::new()->withEnrollment($schoolClass)->create([
            'ano' => $schoolClass->ano,
        ]);
        $multigradeRegistration = LegacyRegistrationFactory::new()->withEnrollment($schoolClass)->create([
            'ano' => $schoolClass->ano,
            'ref_ref_cod_serie' => LegacyGradeFactory::new()->create([
                'ref_cod_curso' => $schoolClass->ref_cod_curso,
            ]),
        ]);
        $user = User::query()->findOrFail(LegacyUserFactory::new()->admin()->create()->getKey());

        $response = $this->callApi('GET', '/module/HistoricoEscolar/processamentoApi', [
            'oper' => 'get',
            'att' => 'matriculas',
            'instituicao_id' => $schoolClass->ref_cod_instituicao,
            'escola_id' => $schoolClass->ref_ref_cod_escola,
            'ano' => $schoolClass->ano,
        ], $user);

        $response->assertSuccessful();

        $series = collect($response->json('matriculas'))->pluck('serie_id', 'matricula_id');

        $this->assertCount(2, $series);
        $this->assertSame($schoolClass->ref_ref_cod_serie, $series[$registration->getKey()]);
        $this->assertSame($multigradeRegistration->ref_ref_cod_serie, $series[$multigradeRegistration->getKey()]);
        $this->assertNotSame($schoolClass->ref_ref_cod_serie, $multigradeRegistration->ref_ref_cod_serie);
    }
}
