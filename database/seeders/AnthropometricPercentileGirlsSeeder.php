<?php

namespace Database\Seeders;

use App\Models\AnthropometricPercentile;
use App\Support\Database\XlsxSeeder;

class AnthropometricPercentileGirlsSeeder extends XlsxSeeder
{
    protected $filename = __DIR__ . '/../xls/anthro/bmi-girls-perc-who2007-exp.xlsx';
    protected $model = AnthropometricPercentile::class;

    /**
     * Transform XLSX data to model format for percentiles.
     * 
     * Expected structure: Month, L, M, S, P01, P1, P3, P5, P10, P15, P25, P50, P75, P85, P90, P95, P97, P99, P999
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
            'gender' => 'F', // Girls = Feminino
            'l_value' => $data['L'] ?? null,
            'm_value' => $data['M'] ?? null,
            's_value' => $data['S'] ?? null,
            // Percentiles data
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
            'source' => 'WHO_2007',
        ];
    }

    public function run()
    {
        $this->command->info('Carregando dados de percentis para meninas (girls)...');
        
        if (!file_exists($this->filename)) {
            $this->command->warn("Arquivo não encontrado: {$this->filename}");
            return;
        }

        $count = 0;
        foreach ($this->read() as $data) {
            $transformedData = $this->transformData($data);
            if (!empty($transformedData)) {
                AnthropometricPercentile::updateOrCreate(
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

        $this->command->info("{$count} registros de percentis para meninas processados!");
    }
}