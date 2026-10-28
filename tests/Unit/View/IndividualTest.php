<?php

use App\Models\City;
use App\Models\Country;
use App\Models\Employee;
use App\Models\Individual;
use App\Models\LocalizationZone;
use App\Models\LogUnification;
use App\Models\Nationality;
use App\Models\Person;
use App\Models\Phone;
use App\Models\Religion;
use App\Models\Student;
use App\User;
use Database\Factories\CityFactory;
use Database\Factories\CountryFactory;
use Database\Factories\EmployeeFactory;
use Database\Factories\LegacyEmployeeFactory;
use Database\Factories\LegacyIndividualFactory;
use Database\Factories\LegacyStudentFactory;
use Database\Factories\LegacyUserFactory;
use Database\Factories\ReligionFactory;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Tests\ViewModelTestCase;

uses(ViewModelTestCase::class);

beforeEach(function () {
    $this->viewModel(Individual::class, relations: [
        'person' => Person::class,
        'phone' => Phone::class,
        'unification' => LogUnification::class,
    ]);

    $this->individual = function (array $attributes = []): Individual {
        return Individual::query()->findOrFail(LegacyIndividualFactory::new()->create($attributes)->getKey());
    };
});

ViewModelTestCase::registerModelTests();

test('describes gender nationality registry origin and localization zone', function () {
    $individual = ($this->individual)([
        'sexo' => 'M',
        'nacionalidade' => Nationality::FOREIGN,
        'zona_localizacao_censo' => LocalizationZone::RURAL,
    ]);

    // O model legado força a origem "M" ao criar; a view traduz a coluna.
    DB::table('cadastro.fisica')->where('idpes', $individual->id)->update(['origem_gravacao' => 'U']);
    $individual->refresh();

    $this->assertSame('Masculino', $individual->gender_description);
    $this->assertSame('Estrangeira', $individual->nationality_description);
    $this->assertSame('Unificação', $individual->registry_origin_description);
    $this->assertSame('Rural', $individual->localization_zone_description);
});

test('real name prefers the social name', function () {
    $this->assertSame('Bia', ($this->individual)(['nome_social' => 'Bia'])->real_name);

    $plain = ($this->individual)(['nome_social' => null]);

    $this->assertSame($plain->person->name, $plain->real_name);
});

test('parent and guardian names come from the linked people', function () {
    $mother = LegacyIndividualFactory::new()->create(['nome_social' => 'Mãe Social']);
    $father = LegacyIndividualFactory::new()->create();
    $guardian = LegacyIndividualFactory::new()->create();

    $individual = ($this->individual)([
        'idpes_mae' => $mother,
        'idpes_pai' => $father,
        'idpes_responsavel' => $guardian,
        'nome_mae' => 'Mãe Digitada',
    ]);

    $this->assertSame('Mãe Social', $individual->mother_name);
    $this->assertSame($father->person->name, $individual->father_name);
    $this->assertSame($guardian->person->name, $individual->guardian_name);
});

test('parent and guardian names fall back to the typed names', function () {
    $individual = ($this->individual)([
        'nome_mae' => 'Mãe Digitada',
        'nome_pai' => 'Pai Digitado',
        'nome_responsavel' => 'Responsável Digitado',
    ]);

    $this->assertSame('Mãe Digitada', $individual->mother_name);
    $this->assertSame('Pai Digitado', $individual->father_name);
    $this->assertSame('Responsável Digitado', $individual->guardian_name);
});

test('formats the cpf', function () {
    $this->assertSame('123.456.789-09', ($this->individual)(['cpf' => 12345678909])->cpf);
});

test('student user and employee share the individual key', function () {
    $individual = ($this->individual)();

    LegacyStudentFactory::new()->create(['ref_idpes' => $individual->id]);
    LegacyEmployeeFactory::new()->create(['ref_cod_pessoa_fj' => $individual->id]);
    LegacyUserFactory::new()->create(['cod_usuario' => $individual->id]);
    EmployeeFactory::new()->create(['id' => $individual->id]);

    $this->assertInstanceOf(Student::class, $individual->student);
    $this->assertSame($individual->id, $individual->student->individual_id);
    $this->assertInstanceOf(User::class, $individual->user);
    $this->assertSame($individual->id, $individual->user->getKey());
    $this->assertInstanceOf(Employee::class, $individual->employee);
    $this->assertSame($individual->id, $individual->employee->getKey());
});

test('country city religion and the people who registered it', function () {
    // A coluna legada do país guarda só três dígitos.
    $country = CountryFactory::new()->create(['id' => 76]);
    $city = CityFactory::new()->create();
    $religion = ReligionFactory::new()->create();
    $creator = LegacyIndividualFactory::new()->create();
    $editor = LegacyIndividualFactory::new()->create();
    $remover = LegacyIndividualFactory::new()->create();

    $individual = ($this->individual)([
        'idpais_estrangeiro' => $country,
        'idmun_nascimento' => $city,
        'ref_cod_religiao' => $religion,
        'idpes_cad' => $creator,
        'idpes_rev' => $editor,
        'ref_usuario_exc' => $remover,
    ]);

    $this->assertInstanceOf(Country::class, $individual->country);
    $this->assertSame($country->getKey(), $individual->country->getKey());
    $this->assertInstanceOf(City::class, $individual->city);
    $this->assertSame($city->getKey(), $individual->city->getKey());
    $this->assertInstanceOf(Religion::class, $individual->religion);
    $this->assertSame($religion->getKey(), $individual->religion->getKey());
    $this->assertSame($creator->getKey(), $individual->createdBy->id);
    $this->assertSame($editor->getKey(), $individual->updatedBy->id);
    $this->assertSame($remover->getKey(), $individual->deletedBy->id);
});

// Defeito conhecido: a relação lê `idmun_nascimento`, mas a view expõe a
// coluna como `city_id`; o local de nascimento nunca é encontrado.
test('defeito birthplace le uma coluna que a view nao tem', function () {
    $this->assertNull(($this->individual)(['idmun_nascimento' => CityFactory::new()->create()])->birthplace);
});

// Defeito conhecido: a relação procura `person_id` em `places`, que não tem
// essa coluna; o endereço da pessoa vem por `Person::place()`.
test('defeito place procura uma coluna que a tabela nao tem', function () {
    $this->expectException(QueryException::class);

    ($this->individual)()->place;
});
