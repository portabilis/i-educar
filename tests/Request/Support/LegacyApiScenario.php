<?php

namespace Tests\Request\Support;

use Database\Factories\LegacyRegistrationFactory;
use Database\Factories\LegacySchoolClassFactory;
use Database\Factories\LegacyUserFactory;
use Tests\TestCase;

/**
 * Cenário comum dos testes das APIs legadas em /module: um administrador
 * logado, uma turma com uma matrícula enturmada e os parâmetros que os
 * recursos da API costumam ler.
 */
final class LegacyApiScenario
{
    /**
     * Grava no teste o usuário (user), a turma (schoolClass), a matrícula
     * (registration) e os parâmetros (params).
     */
    public static function create(TestCase $test): void
    {
        $test->user = LegacyUserFactory::new()->admin()->create();
        $test->actingAs($test->user);

        $test->schoolClass = LegacySchoolClassFactory::new()->create();
        $test->registration = LegacyRegistrationFactory::new()->withEnrollment($test->schoolClass)->create(['ativo' => 1, 'ultima_matricula' => 1]);

        // Os parâmetros que os recursos da API costumam ler; cada recurso usa os seus.
        $test->params = [
            'access_key' => config('legacy.apis.access_key'),
            'secret_key' => config('legacy.apis.secret_key'),
            'instituicao_id' => $test->schoolClass->ref_cod_instituicao,
            'escola_id' => $test->schoolClass->ref_ref_cod_escola,
            'curso_id' => $test->schoolClass->ref_cod_curso,
            'serie_id' => $test->schoolClass->ref_ref_cod_serie,
            'turma_id' => $test->schoolClass->getKey(),
            'ano' => $test->schoolClass->ano,
            'ano_letivo' => $test->schoolClass->ano,
            'matricula_id' => $test->registration->getKey(),
            'aluno_id' => $test->registration->ref_cod_aluno,
            'query' => 'a',
        ];
    }

    /**
     * Registra o teste que confere que cada recurso da API responde com o
     * próprio nome.
     *
     * @param  array<int, string>  $resources
     */
    public static function registerResourceTests(string $endpoint, array $resources): void
    {
        test('responde ao recurso', function (string $resource) use ($endpoint) {
            $this->get($endpoint . '?' . http_build_query(['oper' => 'get', 'resource' => $resource] + $this->params))
                ->assertOk()
                ->assertJsonPath('resource', $resource);
        })->with($resources);
    }
}
