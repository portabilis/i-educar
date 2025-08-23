<?php

namespace Database\Seeders;

class AnthropometricZScoreGirlsSeeder extends AnthropometricZScoreSeeder
{
    public function run()
    {
        AnthropometricZScoreSeeder::forGender('F')->setCommand($this->command)->runSeeder();
    }
}