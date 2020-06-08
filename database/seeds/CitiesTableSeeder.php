<?php

use App\Models\City;
use App\Support\Database\CsvSeeder;

class CitiesTableSeeder extends CsvSeeder
{
    protected $filename = __DIR__ . '/../csvs/cities.csv';

    protected $model = City::class;
}
