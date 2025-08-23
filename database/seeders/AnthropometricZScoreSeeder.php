<?php

namespace Database\Seeders;

use App\Models\AnthropometricZScore;

class AnthropometricZScoreSeeder extends AbstractAnthropometricSeeder
{
    protected $model = AnthropometricZScore::class;
    protected string $typeDescription = 'LMS';

    /**
     * Run both boys and girls seeders
     */
    public function run()
    {
        // Seed boys data
        self::forGender('M')->setCommand($this->command)->runSeeder();
        
        // Seed girls data  
        self::forGender('F')->setCommand($this->command)->runSeeder();
    }

    /**
     * Create seeder for specific gender
     */
    public static function forGender(string $gender): self
    {
        $instance = new static();
        $instance->gender = strtoupper($gender);
        $instance->filename = $instance->getFilenameForGender($gender);
        return $instance;
    }

    /**
     * Set command instance for output
     */
    public function setCommand($command): self
    {
        $this->command = $command;
        return $this;
    }

    /**
     * Get filename based on gender
     */
    private function getFilenameForGender(string $gender): string
    {
        $genderSuffix = strtolower($gender === 'M' ? 'boys' : 'girls');
        return __DIR__ . "/../xls/anthro/bmi-{$genderSuffix}-z-who-2007-exp.xlsx";
    }

    /**
     * Get Z-score specific fields
     */
    protected function getSpecificFields(array $data): array
    {
        return [
            'sd4neg' => $data['SD4neg'] ?? null,
            'sd3neg' => $data['SD3neg'] ?? null,
            'sd2neg' => $data['SD2neg'] ?? null,
            'sd1neg' => $data['SD1neg'] ?? null,
            'sd0' => $data['SD0'] ?? null,
            'sd1' => $data['SD1'] ?? null,
            'sd2' => $data['SD2'] ?? null,
            'sd3' => $data['SD3'] ?? null,
            'sd4' => $data['SD4'] ?? null,
        ];
    }
}