<?php

use App\Models\State;
use App\Support\Database\CsvSeeder;

class StatesTableSeeder extends CsvSeeder
{
    protected $filename = __DIR__ . '/../csvs/states.csv';

    protected $model = State::class;
}
