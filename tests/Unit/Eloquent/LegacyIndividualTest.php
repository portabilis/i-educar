<?php

use App\Models\City;
use App\Models\LegacyDeficiency;
use App\Models\LegacyDocument;
use App\Models\LegacyIndividual;
use App\Models\LegacyIndividualPicture;
use App\Models\LegacyMaritalStatus;
use App\Models\LegacyPerson;
use App\Models\LegacyPhone;
use App\Models\LegacyRace;
use App\Models\LegacyStudent;
use App\Models\Nationality;
use App\Models\Religion;
use Database\Factories\CityFactory;
use Database\Factories\DocumentAbsenceReasonFactory;
use Database\Factories\LegacyIndividualFactory;
use Database\Factories\LegacyRaceFactory;
use Database\Factories\ReligionFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Tests\EloquentModelTestCase;

uses(EloquentModelTestCase::class);

beforeEach(fn () => $this->eloquent(
    LegacyIndividual::class,
    relations: [
        'race' => LegacyRace::class,
        'deficiency' => LegacyDeficiency::class,
        'person' => LegacyPerson::class,
        'student' => LegacyStudent::class,
        'document' => LegacyDocument::class,
        'picture' => LegacyIndividualPicture::class,
        'races' => LegacyRace::class,
        'phones' => LegacyPhone::class,
        'phone' => LegacyPhone::class,
        'maritalStatus' => LegacyMaritalStatus::class,
        'religion' => Religion::class,
    ],
    factoryModifier: fn (Factory $factory) => $factory->state([
        'ref_cod_religiao' => fn () => ReligionFactory::new()->create(),
    ]),
));

EloquentModelTestCase::registerModelTests();

test('informing cpf clears absence reason', function () {
    $individual = LegacyIndividualFactory::new()->create([
        'document_absence_reason_id' => DocumentAbsenceReasonFactory::new(),
        'document_absence_observation' => 'Documentos perdidos',
    ]);

    $individual->update(['cpf' => 81388232758]);
    $individual->refresh();

    $this->assertNull($individual->document_absence_reason_id);
    $this->assertNull($individual->document_absence_observation);
});

test('informing zero cpf keeps absence reason', function () {
    $reason = DocumentAbsenceReasonFactory::new()->create();
    $individual = LegacyIndividualFactory::new()->create([
        'document_absence_reason_id' => $reason->getKey(),
    ]);

    $individual->update(['cpf' => 0]);

    $this->assertSame($reason->getKey(), $individual->refresh()->document_absence_reason_id);
});

test('possui cpf follows the cpf', function () {
    $individual = LegacyIndividualFactory::new()->create(['cpf' => 81388232758]);
    $this->assertTrue($individual->refresh()->possui_cpf);

    $individual->update(['cpf' => 0]);
    $this->assertFalse($individual->refresh()->possui_cpf);

    $individual->update(['cpf' => null]);
    $this->assertFalse($individual->refresh()->possui_cpf);
});

test('saving without changing cpf keeps absence reason', function () {
    $reason = DocumentAbsenceReasonFactory::new()->create();
    $individual = LegacyIndividualFactory::new()->create([
        'document_absence_reason_id' => $reason->getKey(),
    ]);

    LegacyIndividual::find($individual->getKey(), ['idpes', 'ativo'])->update(['ativo' => 0]);
    $individual->refresh();

    $this->assertSame($reason->getKey(), $individual->document_absence_reason_id);
});

test('nationality name is null without nationality', function () {
    $individual = LegacyIndividualFactory::new()->create(['nacionalidade' => null]);

    $this->assertNull($individual->refresh()->nationality_name);
});

test('nationality name describes the nationality', function () {
    $individual = LegacyIndividualFactory::new()->create(['nacionalidade' => Nationality::FOREIGN]);

    $this->assertSame('Estrangeira', $individual->refresh()->nationality_name);
});

test('city birth', function () {
    $city = CityFactory::new()->create();
    $individual = LegacyIndividualFactory::new()->create(['idmun_nascimento' => $city]);

    $this->assertInstanceOf(City::class, $individual->cityBirth);
    $this->assertSame($city->getKey(), $individual->cityBirth->getKey());
});

test('real name prefers the social name', function () {
    $this->assertSame('Bia', LegacyIndividualFactory::new()->create(['nome_social' => 'Bia'])->real_name);

    $plain = LegacyIndividualFactory::new()->create(['nome_social' => null]);

    $this->assertSame($plain->person->name, $plain->real_name);
});

test('parents name joins father and mother', function () {
    $individual = LegacyIndividualFactory::new()->father()->mother()->create();

    $this->assertSame($individual->father->name . ' e ' . $individual->mother->name, $individual->parents_name);
});

test('filters by race cpf and birthdate', function () {
    $race = LegacyRaceFactory::new()->create();
    $ana = LegacyIndividualFactory::new()->create(['cpf' => 12345678909, 'data_nasc' => '2000-01-02']);
    $bia = LegacyIndividualFactory::new()->create(['cpf' => 98765432100, 'data_nasc' => '2001-03-04']);
    $ana->races()->attach($race);

    $ids = fn ($query) => $query->whereIn('idpes', [$ana->getKey(), $bia->getKey()])->pluck('idpes')->all();

    $this->assertSame([$ana->getKey()], $ids(LegacyIndividual::query()->whereRace($race->getKey())));
    $this->assertSame([$bia->getKey()], $ids(LegacyIndividual::query()->whereCpf(98765432100)));
    $this->assertSame([$ana->getKey()], $ids(LegacyIndividual::query()->whereBirthdate('2000-01-02')));
});
