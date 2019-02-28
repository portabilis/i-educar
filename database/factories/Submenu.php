<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\Submenu::class, function (Faker $faker) {
    return [
        'nm_submenu' => $faker->words(2, true),
        'ref_cod_menu_menu' => function() {
            return factory(\App\Models\Menu::class)->create()->cod_menu_menu;
        },
        'cod_sistema' => 2,
        'nivel' => $faker->randomElement([2,3]),
    ];
});
