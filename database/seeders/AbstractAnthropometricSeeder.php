<?php

namespace Database\Seeders;

use App\Support\Database\XlsxSeeder;

abstract class AbstractAnthropometricSeeder extends XlsxSeeder
{
    /**
     * Gender code (M/F)
     */
    protected string $gender;

    /**
     * Type description for user feedback
     */
    protected string $typeDescription;

    /**
     * Data source identifier
     */
    protected string $source = 'WHO_2007';

    /**
     * File type pattern for XLSX files
     */
    abstract protected function getFileTypePattern(): string;

    /**
     * Run both boys and girls seeders
     */
    public function run(): void
    {
        // Seed boys data
        static::forGender('M')->setCommand($this->command)->runSeeder();
        
        // Seed girls data
        static::forGender('F')->setCommand($this->command)->runSeeder();
    }

    /**
     * Create seeder for specific gender
     */
    public static function forGender(string $gender): static
    {
        $instance = new static();
        $instance->gender = strtoupper($gender);
        $instance->filename = $instance->getFilenameForGender($gender);
        return $instance;
    }

    /**
     * Set command instance for output
     */
    public function setCommand($command): static
    {
        $this->command = $command;
        return $this;
    }

    /**
     * Get filename based on gender
     */
    protected function getFilenameForGender(string $gender): string
    {
        $genderSuffix = strtolower($gender === 'M' ? 'boys' : 'girls');
        $pattern = $this->getFileTypePattern();
        return __DIR__ . "/../xls/anthro/bmi-{$genderSuffix}-{$pattern}.xlsx";
    }

    /**
     * Base transformation logic for anthropometric data
     */
    protected function transformData(array $data): array
    {
        // Skip empty rows
        if (empty($data['Month']) || !is_numeric($data['Month'])) {
            return [];
        }

        $baseData = [
            'age_months' => (int) $data['Month'],
            'gender' => $this->gender,
            'l_value' => $data['L'] ?? null,
            'm_value' => $data['M'] ?? null,
            's_value' => $data['S'] ?? null,
            'source' => $this->source,
        ];

        return array_merge($baseData, $this->getSpecificFields($data));
    }

    /**
     * Get fields specific to each type (Z-scores vs Percentiles)
     */
    abstract protected function getSpecificFields(array $data): array;

    /**
     * Unified seeder method
     */
    public function runSeeder(): void
    {
        $this->command->info("Carregando dados {$this->typeDescription} para {$this->getGenderDescription()}...");

        if (!file_exists($this->filename)) {
            $this->command->warn("Arquivo não encontrado: {$this->filename}");
            return;
        }

        try {
            parent::run(); // Call XlsxSeeder's run method
            $this->command->info("Dados {$this->typeDescription} carregados com sucesso para {$this->getGenderDescription()}!");
        } catch (\Exception $e) {
            $this->command->error("Erro ao carregar dados {$this->typeDescription} para {$this->getGenderDescription()}: " . $e->getMessage());
        }
    }

    /**
     * Get gender description for user feedback
     */
    private function getGenderDescription(): string
    {
        return $this->gender === 'M' ? 'meninos' : 'meninas';
    }
}
