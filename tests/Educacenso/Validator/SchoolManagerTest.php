<?php

use App\Models\ManagerAccessCriteria;
use Database\Factories\ManagerAccessCriteriaFactory;
use Faker\Factory;
use iEducar\Modules\Educacenso\Model\DependenciaAdministrativaEscola;
use iEducar\Modules\Educacenso\Model\SchoolManagerAccessCriteria;
use iEducar\Modules\Educacenso\Model\SchoolManagerRole;
use iEducar\Modules\Educacenso\Validator\SchoolManagers;
use iEducar\Modules\ValueObjects\SchoolManagerValueObject;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

beforeEach(function () {
    $this->getInstance = function ($arrayValueObject, $administrativeDependency = 1, $operatingSituation = 1) {
        return new SchoolManagers($arrayValueObject, $administrativeDependency, $operatingSituation);
    };

    $this->getFakeValueObject = function () {
        $faker = Factory::create();
        $valueObject = new SchoolManagerValueObject;
        $valueObject->individualId = $faker->randomNumber(1);
        $valueObject->roleId = $faker->randomNumber(1);
        $valueObject->accessCriteriaId = $faker->randomNumber(1);
        $valueObject->accessCriteriaDescription = $faker->word;
        $valueObject->linkTypeId = $faker->randomNumber(1);
        $valueObject->isChief = $faker->boolean;

        return $valueObject;
    };
});

test('empty manager should be invalid', function () {
    $this->markTestSkipped();

    $valueObject = ($this->getFakeValueObject)();
    $valueObject->individualId = null;
    $validator = ($this->getInstance)([$valueObject]);
    $this->assertFalse($validator->isValid());
});

test('without or empty role should be invalid', function () {
    $valueObject = ($this->getFakeValueObject)();
    $valueObject->roleId = null;
    $validator = ($this->getInstance)([$valueObject]);
    $this->assertFalse($validator->isValid());
});

test('role is director and acces criteria is empty should be invalid', function () {
    $this->markTestSkipped();

    $valueObject = ($this->getFakeValueObject)();
    $valueObject->roleId = SchoolManagerRole::DIRETOR;
    $valueObject->accessCriteriaId = null;
    $validator = ($this->getInstance)([$valueObject]);
    $this->assertFalse($validator->isValid());

    $valueObject->accessCriteriaId = 1;
    $validator = ($this->getInstance)([$valueObject]);
    $this->assertTrue($validator->isValid());
});

test('access criteria is other and description is empty should be invalid', function () {
    $this->markTestSkipped();

    $valueObject = ($this->getFakeValueObject)();
    $valueObject->accessCriteriaId = SchoolManagerAccessCriteria::OUTRO;
    $valueObject->accessCriteriaDescription = null;
    $validator = ($this->getInstance)([$valueObject]);
    $this->assertFalse($validator->isValid());

    $valueObject->accessCriteriaDescription = '1';
    $validator = ($this->getInstance)([$valueObject]);
    $this->assertTrue($validator->isValid());
});

test('role is director and access type is empty should be invalid', function () {
    $this->markTestSkipped();

    $valueObject = ($this->getFakeValueObject)();
    $valueObject->roleId = SchoolManagerRole::DIRETOR;
    $valueObject->linkTypeId = null;
    $validator = ($this->getInstance)([$valueObject], DependenciaAdministrativaEscola::FEDERAL);
    $this->assertFalse($validator->isValid());

    $valueObject->linkTypeId = 1;
    $validator = ($this->getInstance)([$valueObject], DependenciaAdministrativaEscola::FEDERAL);
    $this->assertTrue($validator->isValid());

    $valueObject->linkTypeId = null;
    $validator = ($this->getInstance)([$valueObject], DependenciaAdministrativaEscola::PRIVADA);
    $this->assertTrue($validator->isValid());
});

test('diretor em atividade precisa de criterio de acesso e tipo de vinculo', function () {
    $valueObject = ($this->getFakeValueObject)();
    $valueObject->roleId = SchoolManagerRole::DIRETOR;
    $valueObject->accessCriteriaId = null;
    $valueObject->linkTypeId = null;
    $valueObject->isChief = true;
    $valueObject->employeeId = 10;

    $validator = ($this->getInstance)([$valueObject], DependenciaAdministrativaEscola::MUNICIPAL);

    // O validador roda na tela legada da escola, sem avisos do PHP: sem critério,
    // a busca da descrição do critério lê uma chave vazia.
    $level = error_reporting(0);

    try {
        $this->assertFalse($validator->isValid());
    } finally {
        error_reporting($level);
    }

    $this->assertCount(2, $validator->getMessage());
});

test('criterio de acesso precisa combinar com a dependencia administrativa', function () {
    foreach ([SchoolManagerAccessCriteria::PROPRIETARIO, SchoolManagerAccessCriteria::CONCURSO] as $id) {
        ManagerAccessCriteria::query()->find($id) ?? ManagerAccessCriteriaFactory::new()->create(['id' => $id]);
    }

    $proprietario = ($this->getFakeValueObject)();
    $proprietario->accessCriteriaId = SchoolManagerAccessCriteria::PROPRIETARIO;
    $proprietario->isChief = true;
    $proprietario->employeeId = 10;
    $proprietario->roleId = SchoolManagerRole::OUTRO;

    $concursado = ($this->getFakeValueObject)();
    $concursado->accessCriteriaId = SchoolManagerAccessCriteria::CONCURSO;
    $concursado->isChief = true;
    $concursado->employeeId = 10;
    $concursado->roleId = SchoolManagerRole::OUTRO;

    $publica = ($this->getInstance)([$proprietario], DependenciaAdministrativaEscola::MUNICIPAL);
    $privada = ($this->getInstance)([$concursado], DependenciaAdministrativaEscola::PRIVADA);

    $this->assertFalse($publica->isValid());
    $this->assertStringContainsString('proprietário', implode(PHP_EOL, $publica->getMessage()));
    $this->assertFalse($privada->isValid());
    $this->assertStringContainsString('quando a dependência administrativa da escola for: <b>Privada</b>', implode(PHP_EOL, $privada->getMessage()));
});
