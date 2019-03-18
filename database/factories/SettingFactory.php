<?php

use App\Setting;
use Faker\Generator as Faker;

$factory->define(Setting::class, function (Faker $faker) {

    $type = $faker->randomElement([
        Setting::TYPE_STRING,
        Setting::TYPE_INTEGER,
        Setting::TYPE_FLOAT,
        Setting::TYPE_BOOLEAN,
    ]);

    if ($type === Setting::TYPE_STRING) {
        $value = $faker->words(3, true);
    } elseif ($type === Setting::TYPE_INTEGER) {
        $value = $faker->numberBetween(0, 1000000);
    } elseif ($type === Setting::TYPE_FLOAT) {
        $value = $faker->randomFloat(2, 10, 100);
    } elseif ($type === Setting::TYPE_BOOLEAN) {
        $value = $faker->boolean;
    }

    return [
        'key' => $faker->unique()->word,
        'value' => $value,
        'type' => $faker->randomElement(['string', 'integer', 'float', 'boolean']),
        'description' => $faker->words(3, true),
    ];
});
