<?php

use App\Models\LegacyOrganization;
use App\Models\LegacyPerson;
use App\Models\LegacyUser;
use Faker\Generator as Faker;

$factory->define(LegacyOrganization::class, function (Faker $faker) {

    $person = factory(LegacyPerson::class)->create([
        'nome' => $faker->company,
    ]);

    return [
        'idpes' => $person,
        'cnpj' => $faker->numerify('##############'),
        'insc_estadual' => $faker->numerify('########'),
        'origem_gravacao' => $faker->randomElement(['M', 'U', 'C', 'O']),
        'idpes_cad' => factory(LegacyUser::class)->state('unique')->make(),
        'data_cad' => now(),
        'operacao' => $faker->randomElement(['I', 'A', 'E']),
        'fantasia' => $person->name,
    ];
});
