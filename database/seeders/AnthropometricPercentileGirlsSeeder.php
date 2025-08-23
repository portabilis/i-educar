<?php

namespace Database\Seeders;

class AnthropometricPercentileGirlsSeeder extends AnthropometricPercentileSeeder
{
    public function run()
    {
        AnthropometricPercentileSeeder::forGender('F')->run();
    }
}