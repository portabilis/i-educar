<?php

use App\Models\LegacyMenu;
use App\Models\LegacySubmenu;
use Faker\Generator as Faker;

$factory->define(LegacySubmenu::class, function (Faker $faker) {
    return [
        'nm_submenu' => $faker->words(2, true),
        'ref_cod_menu_menu' => function() {
            return factory(LegacyMenu::class)->create()->cod_menu_menu;
        },
        'cod_sistema' => 2,
        'nivel' => $faker->randomElement([2,3]),
    ];
});
