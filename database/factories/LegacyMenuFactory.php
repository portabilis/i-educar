<?php

use App\Models\LegacyMenu;
use Faker\Generator as Faker;

$factory->define(LegacyMenu::class, function (Faker $faker) {
    return [
        'nm_menu' => $faker->words(1, true),
        'title' => $faker->words(1, true),
        'ativo' => 1
    ];
});
