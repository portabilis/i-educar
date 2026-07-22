<?php

use App\Models\EducacensoIndigenousPeople;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $file = file(database_path('csvs/educacenso/create_povos_indigenas.csv'));

        foreach ($file as $line) {
            $data = str_getcsv(
                string: $line,
                separator: ';'
            );

            EducacensoIndigenousPeople::query()
                ->updateOrCreate([
                    'id' => $data[0],
                ], [
                    'name' => $data[1],
                ]);
        }
    }
};
