<?php

namespace Database\Seeders;

use App\Models\BNCC;
use App\Support\Database\CsvSeeder;

class BNCCTableSeeder extends CsvSeeder
{
    protected $filename = __DIR__ . '/../csvs/bncc.csv';

    protected $model = BNCC::class;
}
