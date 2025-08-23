<?php

namespace Database\Seeders;

class AnthropometricZScoreBoysSeeder extends AnthropometricZScoreSeeder
{
    public function run(): void
    {
        AnthropometricZScoreSeeder::forGender('M')->setCommand($this->command)->runSeeder();
    }
}