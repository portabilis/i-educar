<?php

use App\Models\LegacyIndividual;
use Database\Factories\LegacyIndividualFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

test('filtra pessoas com nome e data de nascimento semelhantes', function () {
    $similar = LegacyIndividualFactory::new()->withName('João da Silva')->create(['data_nasc' => '2015-04-20']);
    $differentName = LegacyIndividualFactory::new()->withName('João da Silva Filho')->create(['data_nasc' => '2015-04-20']);
    $differentBirthdate = LegacyIndividualFactory::new()->withName('João da Silva')->create(['data_nasc' => '2016-04-20']);

    $ids = LegacyIndividual::query()
        ->whereKey([$similar->getKey(), $differentName->getKey(), $differentBirthdate->getKey()])
        ->whereSimilarNameAndBirthdate('JOAO DA SILVA', '2015-04-20')
        ->pluck('idpes')
        ->all();

    expect($ids)->toBe([$similar->getKey()]);
});

test('permite ignorar uma pessoa semelhante', function () {
    $individual = LegacyIndividualFactory::new()->withName('João da Silva')->create(['data_nasc' => '2015-04-20']);

    $exists = LegacyIndividual::query()
        ->whereSimilarNameAndBirthdate('João da Silva', '2015-04-20', $individual->getKey())
        ->whereKey($individual->getKey())
        ->exists();

    expect($exists)->toBeFalse();
});
