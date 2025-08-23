<?php

namespace Database\Seeders;

use App\Models\AnthropometricZScore;
use App\Support\Database\XlsxSeeder;

class AnthropometricZScoreBoysSeeder extends XlsxSeeder
{
    protected $filename = __DIR__ . '/../xls/anthro/bmi-boys-z-who-2007-exp.xlsx';
    protected $model = AnthropometricZScore::class;

    /**
     * Transform XLSX data to model format.
     * 
     * Expected structure: Month, L, M, S, SD4neg, SD3neg, SD2neg, SD1neg, SD0, SD1, SD2, SD3, SD4
     *
     * @param array $data
     * @return array
     */
    protected function transformData(array $data): array
    {
        // Skip empty rows
        if (empty($data['Month']) || !is_numeric($data['Month'])) {
            return [];
        }

        return [
            'age_months' => (int) $data['Month'],
            'gender' => 'M', // Boys = Masculino
            'l_value' => $data['L'] ?? null,
            'm_value' => $data['M'] ?? null,
            's_value' => $data['S'] ?? null,
            'sd4neg' => $data['SD4neg'] ?? null,
            'sd3neg' => $data['SD3neg'] ?? null,
            'sd2neg' => $data['SD2neg'] ?? null,
            'sd1neg' => $data['SD1neg'] ?? null,
            'sd0' => $data['SD0'] ?? null,
            'sd1' => $data['SD1'] ?? null,
            'sd2' => $data['SD2'] ?? null,
            'sd3' => $data['SD3'] ?? null,
            'sd4' => $data['SD4'] ?? null,
            'source' => 'WHO_2007',
        ];
    }

    public function run()
    {
        $this->command->info('Carregando dados LMS para meninos (boys)...');
        
        if (!file_exists($this->filename)) {
            $this->command->warn("Arquivo não encontrado: {$this->filename}");
            return;
        }

        $count = 0;
        foreach ($this->read() as $data) {
            $transformedData = $this->transformData($data);
            if (!empty($transformedData)) {
                AnthropometricZScore::updateOrCreate(
                    [
                        'age_months' => $transformedData['age_months'],
                        'gender' => $transformedData['gender'],
                        'source' => $transformedData['source'],
                    ],
                    $transformedData
                );
                $count++;
            }
        }

        $this->command->info("{$count} registros de meninos carregados com sucesso!");
    }
}