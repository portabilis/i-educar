<?php

use Faker\Generator as Faker;

$factory->define(\App\Entities\Menu::class, function (Faker $faker) {
    return [
        'nm_menu' => $faker->words(1, true),
        'title' => $faker->words(1, true),
        'ativo' => 1
    ];
});
