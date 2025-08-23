<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Support\AnthropometricStatistics;

class AnthropometricDataSeeder extends Seeder
{
    /**
     * Seed all anthropometric reference data.
     */
    public function run()
    {
        $this->command->info('Iniciando carregamento de dados antropométricos WHO 2007...');
        $this->command->newLine();

        // 1. Carregar dados Z-scores para meninos e meninas
        $this->command->info('ETAPA 1: Dados Z-score');
        $this->call(AnthropometricZScoreBoysSeeder::class);
        $this->command->newLine();

        $this->call(AnthropometricZScoreGirlsSeeder::class);
        $this->command->newLine();

        // 2. Carregar dados de percentis para meninos e meninas
        $this->command->info('ETAPA 2: Dados de Percentis');
        $this->call(AnthropometricPercentileBoysSeeder::class);
        $this->command->newLine();

        $this->call(AnthropometricPercentileGirlsSeeder::class);
        $this->command->newLine();

        // Dados de circunferência da cintura agora vêm do P90 da tabela de percentis
        $this->command->info('Dados de circunferência: Usando P90 da tabela de percentis');

        $this->command->info('Todos os dados antropométricos foram carregados com sucesso!');
        $this->command->newLine();

        // Mostrar estatísticas
        $this->showStatistics();
    }

    /**
     * Show loading statistics.
     */
    protected function showStatistics()
    {
        $tableData = AnthropometricStatistics::getTableData();
        // Adjust headers for seeder context
        $this->command->table(['Tipo de Dados', 'Quantidade', 'Descrição'], $tableData);

        foreach (AnthropometricStatistics::getAgeRangeMessages() as $message) {
            $this->command->info($message);
        }

        // Verificar completude dos dados
        $this->command->newLine();
        $this->command->info('Verificação de Completude:');
        
        $completeness = AnthropometricStatistics::getCompletenessStatus();
        switch ($completeness['status']) {
            case 'complete':
                $this->command->info($completeness['message']);
                break;
            case 'partial':
                $this->command->warn($completeness['message']);
                break;
            case 'empty':
                $this->command->error($completeness['message']);
                break;
            default:
                $this->command->warn('Status de completude desconhecido');
                break;
        }
    }
}
