<?php

namespace Database\Seeders;

class AnthropometricZScoreBoysSeeder extends AnthropometricZScoreSeeder
{
    public function run()
    {
        AnthropometricZScoreSeeder::forGender('M')->setCommand($this->command)->runSeeder();
    }
}