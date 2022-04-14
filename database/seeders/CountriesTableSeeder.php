<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Support\Database\CsvSeeder;

class CountriesTableSeeder extends CsvSeeder
{
    protected $filename = __DIR__ . '/../csvs/countries.csv';

    protected $model = Country::class;
}
