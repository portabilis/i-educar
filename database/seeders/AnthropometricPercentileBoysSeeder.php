<?php

namespace Database\Seeders;

class AnthropometricPercentileBoysSeeder extends AnthropometricPercentileSeeder
{
    public function run()
    {
        AnthropometricPercentileSeeder::forGender('M')->setCommand($this->command)->runSeeder();
    }
}