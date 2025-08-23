<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

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
        $zScoreCount = \App\Models\AnthropometricZScore::count();
        $zScoreBoys = \App\Models\AnthropometricZScore::where('gender', 'M')->count();
        $zScoreGirls = \App\Models\AnthropometricZScore::where('gender', 'F')->count();
        
        $percentileCount = \App\Models\AnthropometricPercentile::count();
        $percentileBoys = \App\Models\AnthropometricPercentile::where('gender', 'M')->count();
        $percentileGirls = \App\Models\AnthropometricPercentile::where('gender', 'F')->count();
        
        $waistCount = $percentileCount > 0 ? $percentileCount : 0; // P90 vem dos percentis
        
        $this->command->table([
            'Tipo de Dados', 'Quantidade', 'Descrição'
        ], [
            ['Z-scores Total', $zScoreCount, 'Total de registros de Z-scores (meninos + meninas)'],
            ['├─ Meninos Z-score', $zScoreBoys, 'Z-scores masculinos'],
            ['└─ Meninas Z-score', $zScoreGirls, 'Z-scores femininos'],
            ['Percentis Total', $percentileCount, 'Total de registros de percentis (meninos + meninas)'],
            ['├─ Meninos Percentis', $percentileBoys, 'Percentis masculinos'],
            ['└─ Meninas Percentis', $percentileGirls, 'Percentis femininos'],
            ['P90 Cintura', $waistCount, 'P90 da circunferência (via percentis)'],
            ['Total Geral', $zScoreCount + $percentileCount, 'Todos os registros carregados'],
        ]);
        
        if ($zScoreCount > 0) {
            $ageRange = \App\Models\AnthropometricZScore::selectRaw('MIN(age_months) as min_age, MAX(age_months) as max_age')->first();
            $minYears = floor($ageRange->min_age / 12);
            $maxYears = floor($ageRange->max_age / 12);
            $this->command->info("Faixa etária Z-scores: {$ageRange->min_age} - {$ageRange->max_age} meses ({$minYears} - {$maxYears} anos)");
        }
        
        if ($percentileCount > 0) {
            $ageRange = \App\Models\AnthropometricPercentile::selectRaw('MIN(age_months) as min_age, MAX(age_months) as max_age')->first();
            $minYears = floor($ageRange->min_age / 12);
            $maxYears = floor($ageRange->max_age / 12);
            $this->command->info("Faixa etária Percentis: {$ageRange->min_age} - {$ageRange->max_age} meses ({$minYears} - {$maxYears} anos)");
        }
        
        if ($percentileCount > 0) {
            $this->command->info("P90 Cintura: Disponível via tabela de percentis (mesma faixa etária)");
        }
        
        // Verificar completude dos dados
        $this->command->newLine();
        $this->command->info('Verificação de Completude:');
        
        if ($zScoreCount > 0 && $percentileCount > 0) {
            $this->command->info("Dados completos: Z-scores E Percentis carregados em tabelas separadas");
        } elseif ($zScoreCount > 0) {
            $this->command->warn("Apenas Z-scores carregados. Faltam percentis.");
        } elseif ($percentileCount > 0) {
            $this->command->warn("Apenas Percentis carregados. Faltam Z-scores.");
        } else {
            $this->command->error("Nenhum dado antropométrico carregado");
        }
    }
}