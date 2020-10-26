<?php

use App\Models\LegacyValueRoundingTable;
use Faker\Generator as Faker;
use App\Models\LegacyRoundingTable;
use Illuminate\Database\Eloquent\Factory;

/** @var Factory $factory */

$factory->define(LegacyValueRoundingTable::class, function (Faker $faker) {
    return [
        'tabela_arredondamento_id' => factory(LegacyRoundingTable::class)->make(),
        'nome' =>$faker->randomNumber(1),
    ];
});
