<?php

namespace Database\Seeders;

use App\Models\BNCCSpecification;
use App\Support\Database\CsvSeeder;

class BNCCSpecificationTableSeeder extends CsvSeeder
{
    protected $filename = __DIR__ . '/../csvs/bncc_especificacao.csv';

    protected $model = BNCCSpecification::class;
}
