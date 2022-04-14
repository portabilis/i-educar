<?php

namespace Database\Seeders;

use App\Models\District;
use App\Support\Database\CsvSeeder;

class DistrictsTableSeeder extends CsvSeeder
{
    protected $filename = __DIR__ . '/../csvs/districts.csv';

    protected $model = District::class;
}
