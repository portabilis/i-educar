<?php

namespace App\Console\Commands;

use Database\Seeders\AnthropometricDataSeeder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use App\Support\AnthropometricStatistics;

class LoadAnthropometricDataCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'anthropometric:load
                           {--fresh : Truncate tables before loading}
                           {--source=WHO_2007 : Source identifier for the data}
                           {--boys-z-file= : Path to boys Z-score XLSX file}
                           {--girls-z-file= : Path to girls Z-score XLSX file}
                           {--boys-perc-file= : Path to boys percentiles XLSX file}
                           {--girls-perc-file= : Path to girls percentiles XLSX file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Load anthropometric reference data from WHO 2007 XLSX files';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Carregamento de Dados Antropométricos WHO 2007');
        $this->newLine();

        // Check if migrations are up to date
        $this->checkMigrations();

        // Optionally truncate tables
        if ($this->option('fresh')) {
            $this->freshTables();
        }

        // Verify files exist
        if (!$this->verifyDataFiles()) {
            return Command::FAILURE;
        }

        // Run the seeder
        $this->info('Iniciando carregamento dos dados...');

        try {
            Artisan::call('db:seed', [
                '--class' => AnthropometricDataSeeder::class,
                '--force' => true,
            ]);

            $seedOutput = Artisan::output();
            $this->info($seedOutput);

            $this->newLine();
            $this->info('Dados antropométricos carregados com sucesso!');

            // Show final statistics
            $this->showFinalStatistics();

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('Erro ao carregar dados: ' . $e->getMessage());
            $this->error('Stack trace: ' . $e->getTraceAsString());

            return Command::FAILURE;
        }
    }

    /**
     * Check if migrations are up to date.
     */
    private function checkMigrations(): void
    {
        $this->info('Verificando migrations...');

        if (!$this->confirm('As migrations estão atualizadas?', true)) {
            $this->info('Executando migrations...');
            Artisan::call('migrate', ['--force' => true]);
            $this->info(Artisan::output());
        }
    }

    /**
     * Truncate tables if fresh option is used.
     */
    private function freshTables(): void
    {
        $this->warn('Limpando tabelas existentes...');

        if ($this->confirm('Isso apagará todos os dados antropométricos existentes. Continuar?')) {
            \App\Models\AnthropometricZScore::truncate();
            \App\Models\AnthropometricPercentile::truncate();
            $this->info('Tabelas limpas');
        } else {
            $this->info('Operação cancelada');
        }
    }

    /**
     * Verify that required data files exist.
     */
    private function verifyDataFiles(): bool
    {
        $dataDir = database_path('xls/anthro');
        $files = $this->getDataFilesPaths($dataDir);

        $this->info('Verificando arquivos de dados...');

        $issues = $this->checkRequiredFiles($files['required']);
        $warnings = $this->checkOptionalFiles($files['optional']);

        if (!empty($issues)) {
            $this->showFileErrors($issues, $dataDir);
            return false;
        }

        if (!empty($warnings)) {
            $this->showFileWarnings($warnings);
        }

        $this->showFileSummary($files['required'], $files['optional'], $issues, $warnings);
        return true;
    }

    /**
     * Get data files paths
     */
    private function getDataFilesPaths(string $dataDir): array
    {
        return [
            'required' => [
                'Z-score Boys' => $this->option('boys-z-file') ?: $dataDir . '/bmi-boys-z-who-2007-exp.xlsx',
                'Z-score Girls' => $this->option('girls-z-file') ?: $dataDir . '/bmi-girls-z-who-2007-exp.xlsx',
            ],
            'optional' => [
                'Percentiles Boys' => $this->option('boys-perc-file') ?: $dataDir . '/bmi-boys-perc-who2007-exp.xlsx',
                'Percentiles Girls' => $this->option('girls-perc-file') ?: $dataDir . '/bmi-girls-perc-who2007-exp.xlsx',
            ]
        ];
    }

    /**
     * Check required files and return issues
     */
    private function checkRequiredFiles(array $files): array
    {
        $issues = [];
        foreach ($files as $type => $file) {
            if (!file_exists($file)) {
                $issues[] = "Arquivo obrigatório não encontrado ({$type}): {$file}";
            } else {
                $this->info("{$type}: {$file}");
            }
        }
        return $issues;
    }

    /**
     * Check optional files and return warnings
     */
    private function checkOptionalFiles(array $files): array
    {
        $warnings = [];
        foreach ($files as $type => $file) {
            if (!file_exists($file)) {
                $warnings[] = "Arquivo opcional não encontrado ({$type}): {$file}";
            } else {
                $this->info("{$type}: {$file}");
            }
        }
        return $warnings;
    }

    /**
     * Show file errors
     */
    private function showFileErrors(array $issues, string $dataDir): void
    {
        $this->error('Problemas críticos encontrados:');
        foreach ($issues as $issue) {
            $this->error("  - {$issue}");
        }

        $this->newLine();
        $this->info('Para resolver:');
        $this->info('1. Baixe os arquivos WHO 2007 BMI obrigatórios');
        $this->info('2. Coloque-os em: ' . $dataDir);
        $this->info('3. Nomes esperados:');
        $this->info('   - bmi-boys-z-who-2007-exp.xlsx (Z-scores meninos)');
        $this->info('   - bmi-girls-z-who-2007-exp.xlsx (Z-scores meninas)');
        $this->info('4. Arquivos opcionais para dados completos:');
        $this->info('   - bmi-boys-perc-who2007-exp.xlsx (Percentis meninos)');
        $this->info('   - bmi-girls-perc-who2007-exp.xlsx (Percentis meninas)');
    }

    /**
     * Show file warnings
     */
    private function showFileWarnings(array $warnings): void
    {
        $this->warn('Avisos (não críticos):');
        foreach ($warnings as $warning) {
            $this->warn("  - {$warning}");
        }
        $this->warn('  Os dados de percentis não serão carregados, mas o sistema funcionará normalmente.');
        $this->newLine();
    }

    /**
     * Show file summary
     */
    private function showFileSummary(array $required, array $optional, array $issues, array $warnings): void
    {
        $foundFiles = count($required) - count($issues) + (count($optional) - count($warnings));
        $totalFiles = count($required) + count($optional);
        $this->info("Arquivos encontrados: {$foundFiles}/{$totalFiles}");
    }

    /**
     * Show final statistics after loading.
     */
    private function showFinalStatistics(): void
    {
        $this->table(['Tipo', 'Quantidade', 'Detalhes'], AnthropometricStatistics::getTableData());

        foreach (AnthropometricStatistics::getAgeRangeMessages() as $message) {
            $this->info($message);
        }

        $this->newLine();
        $this->info('Para usar os dados em produção, chame:');
        $this->info('   $service = new AnthropometricService();');
        $this->info('   $service->carregarReferenciasDoBanco();');
    }
}
