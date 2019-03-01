<?php

use App\District;
use Faker\Generator as Faker;

$factory->define(App\Neighborhood::class, function (Faker $faker) {
    return [
        'district_id' => function () {

            $district = District::query()->inRandomOrder()->first();

            if (empty($district)) {
                $district = factory(District::class)->create();
            }

            return $district->getKey();
        },
        'name' => $faker->name,
        'zone' => $faker->randomElement([1, 2]),
    ];
});
