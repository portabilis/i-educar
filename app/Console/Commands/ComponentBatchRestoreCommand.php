<?php

namespace App\Console\Commands;

use App\Models\ComponentBatchOperation;
use App\Models\Enums\ComponentBatchStatus;
use App\Services\ComponentBatchManagerService;
use Illuminate\Console\Command;

class ComponentBatchRestoreCommand extends Command
{
    protected $signature = 'batch:restore {id : ID da operação} {--force : Sobrescrever dados existentes com os do backup}';

    protected $description = 'Restaura o backup de uma operação do gerenciamento em lote de componentes';

    public function handle(ComponentBatchManagerService $service): int
    {
        $operation = ComponentBatchOperation::find($this->argument('id'));

        if (!$operation) {
            $this->error('Operação não encontrada.');

            return self::FAILURE;
        }

        if ($operation->status() !== ComponentBatchStatus::COMPLETED) {
            $this->error("Operação com status '{$operation->status()->label()}'. Apenas operações concluídas podem ser restauradas.");

            return self::FAILURE;
        }

        if (empty($operation->backup)) {
            $this->error('Operação não possui backup para restaurar.');

            return self::FAILURE;
        }

        $forceBackup = $this->option('force');

        $data = $operation->data;
        $this->info("Operação #{$operation->id} — criada em {$operation->created_at->format('d/m/Y H:i')}");
        $this->line("  Ano: {$data['year']}");
        $this->line('  Escolas: ' . count($data['school_ids'] ?? []) . ' | Séries: ' . count($data['grade_ids'] ?? []) . ' | Componentes: ' . count($data['discipline_ids'] ?? []));
        $this->newLine();

        $this->displayBackupSummary($operation->backup);

        if ($forceBackup) {
            $this->warn('Modo --force: dados existentes serão sobrescritos pelos valores do backup.');
        } else {
            $this->info('Modo padrão: registros existentes serão mantidos, anos letivos serão mesclados.');
        }

        $this->newLine();

        if (!$this->confirm('Confirma a restauração?')) {
            $this->info('Operação cancelada.');

            return self::SUCCESS;
        }

        try {
            $summary = $service->restore($operation, $forceBackup);

            $this->newLine();
            $this->info('Restauração concluída com sucesso!');
            $this->newLine();

            $tableRows = [];
            foreach ($summary as $table => $info) {
                $label = ComponentBatchManagerService::TABLE_LABELS[$table] ?? $table;
                $tableRows[] = [$label, $info['count'], $info['action']];
            }

            $this->table(['Tabela', 'Registros', 'Ação'], $tableRows);

            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Erro na restauração: ' . $e->getMessage());

            return self::FAILURE;
        }
    }

    private function displayBackupSummary(array $backup): void
    {
        $this->info('Conteúdo do backup:');

        foreach ($backup['deleted'] ?? [] as $table => $meta) {
            $label = ComponentBatchManagerService::TABLE_LABELS[$table] ?? $table;
            $count = count($meta['rows'] ?? []);
            $this->line("  - {$label}: {$count} registros deletados");
        }

        foreach ($backup['updated'] ?? [] as $table => $meta) {
            $label = ComponentBatchManagerService::TABLE_LABELS[$table] ?? $table;
            $count = count($meta['rows'] ?? []);
            $this->line("  - {$label}: {$count} registros atualizados");
        }

        if (!empty($backup['touched'])) {
            $this->line('  - Vínculos professor/turma: tocados (não restaurável)');
        }

        $this->newLine();
    }
}
