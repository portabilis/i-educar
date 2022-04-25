<?php

namespace Database\Seeders;

use App\Support\Database\IncrementSequence;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use IncrementSequence;

    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(CountriesTableSeeder::class);
        $this->call(StatesTableSeeder::class);
        $this->call(CitiesTableSeeder::class);
        $this->call(DistrictsTableSeeder::class);
        //$this->call(DefaultBNCCTableSeeder::class);
        //$this->call(DefaultBNCCSpecificationTableSeeder::class);

        $this->incrementSequence('countries');
        $this->incrementSequence('states');
        $this->incrementSequence('cities');
        $this->incrementSequence('districts');
    }
}
