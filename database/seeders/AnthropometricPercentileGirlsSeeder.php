<?php

namespace Database\Seeders;

class AnthropometricPercentileGirlsSeeder extends AnthropometricPercentileSeeder
{
    public function run(): void
    {
        AnthropometricPercentileSeeder::forGender('F')->setCommand($this->command)->runSeeder();
    }
}