<?php

namespace Database\Seeders;

use App\Models\AnthropometricPercentile;

class AnthropometricPercentileSeeder extends AbstractAnthropometricSeeder
{
    protected $model = AnthropometricPercentile::class;
    protected string $typeDescription = 'percentis';

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
        return __DIR__ . "/../xls/anthro/bmi-{$genderSuffix}-perc-who2007-exp.xlsx";
    }

    /**
     * Get percentile specific fields
     */
    protected function getSpecificFields(array $data): array
    {
        return [
            'p01' => $data['P01'] ?? null,
            'p1' => $data['P1'] ?? null,
            'p3' => $data['P3'] ?? null,
            'p5' => $data['P5'] ?? null,
            'p10' => $data['P10'] ?? null,
            'p15' => $data['P15'] ?? null,
            'p25' => $data['P25'] ?? null,
            'p50' => $data['P50'] ?? null,
            'p75' => $data['P75'] ?? null,
            'p85' => $data['P85'] ?? null,
            'p90' => $data['P90'] ?? null,
            'p95' => $data['P95'] ?? null,
            'p97' => $data['P97'] ?? null,
            'p99' => $data['P99'] ?? null,
            'p999' => $data['P999'] ?? null,
        ];
    }
}