<?php

namespace Database\Seeders;

class AnthropometricPercentileBoysSeeder extends AnthropometricPercentileSeeder
{
    public function run(): void
    {
        AnthropometricPercentileSeeder::forGender('M')->setCommand($this->command)->runSeeder();
    }
}
