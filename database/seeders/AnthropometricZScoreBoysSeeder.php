<?php

namespace Database\Seeders;

use App\Models\AnthropometricZScore;

class AnthropometricZScoreBoysSeeder extends AbstractAnthropometricSeeder
{
    protected $filename = __DIR__ . '/../xls/anthro/bmi-boys-z-who-2007-exp.xlsx';
    protected $model = AnthropometricZScore::class;
    protected string $gender = 'M';
    protected string $typeDescription = 'LMS';

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
