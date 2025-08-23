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
    public function handle()
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
    private function checkMigrations()
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
    private function freshTables()
    {
        $this->warn('Limpando tabelas existentes...');

        if ($this->confirm('Isso apagará todos os dados antropométricos existentes. Continuar?')) {
            \App\Models\AnthropometricZScore::truncate();
            \App\Models\AnthropometricPercentile::truncate();
            $this->info('Tabelas limpas');
        } else {
            $this->info('Operação cancelada');
            exit(Command::FAILURE);
        }
    }

    /**
     * Verify that required data files exist.
     */
    private function verifyDataFiles(): bool
    {
        $dataDir = database_path('xls/anthro');

        // Z-score files
        $boysZFile = $this->option('boys-z-file') ?: $dataDir . '/bmi-boys-z-who-2007-exp.xlsx';
        $girlsZFile = $this->option('girls-z-file') ?: $dataDir . '/bmi-girls-z-who-2007-exp.xlsx';

        // Percentile files
        $boysPercFile = $this->option('boys-perc-file') ?: $dataDir . '/bmi-boys-perc-who2007-exp.xlsx';
        $girlsPercFile = $this->option('girls-perc-file') ?: $dataDir . '/bmi-girls-perc-who2007-exp.xlsx';

        $this->info('Verificando arquivos de dados...');

        $required = [
            'Z-score Boys' => $boysZFile,
            'Z-score Girls' => $girlsZFile,
        ];

        $optional = [
            'Percentiles Boys' => $boysPercFile,
            'Percentiles Girls' => $girlsPercFile,
        ];

        $issues = [];
        $warnings = [];

        // Verificar arquivos obrigatórios
        foreach ($required as $type => $file) {
            if (!file_exists($file)) {
                $issues[] = "Arquivo obrigatório não encontrado ({$type}): {$file}";
            } else {
                $this->info("{$type}: {$file}");
            }
        }

        // Verificar arquivos opcionais
        foreach ($optional as $type => $file) {
            if (!file_exists($file)) {
                $warnings[] = "Arquivo opcional não encontrado ({$type}): {$file}";
            } else {
                $this->info("{$type}: {$file}");
            }
        }

        // Mostrar problemas críticos
        if (!empty($issues)) {
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

            return false;
        }

        // Mostrar avisos para arquivos opcionais
        if (!empty($warnings)) {
            $this->warn('Avisos (não críticos):');
            foreach ($warnings as $warning) {
                $this->warn("  - {$warning}");
            }
            $this->warn('  Os dados de percentis não serão carregados, mas o sistema funcionará normalmente.');
            $this->newLine();
        }

        $foundFiles = count($required) - count($issues) + (count($optional) - count($warnings));
        $totalFiles = count($required) + count($optional);
        $this->info("Arquivos encontrados: {$foundFiles}/{$totalFiles}");

        return true;
    }

    /**
     * Show final statistics after loading.
     */
    private function showFinalStatistics()
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
