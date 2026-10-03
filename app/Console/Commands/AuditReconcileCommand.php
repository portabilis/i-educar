<?php

namespace App\Console\Commands;

use App\Support\Database\AuditTrigger;
use App\Support\Database\Connections;
use Illuminate\Console\Command;
use Throwable;

class AuditReconcileCommand extends Command
{
    use AuditTrigger;
    use Connections;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'audit:reconcile
                            {--dry-run : Apenas informa as diferenças, sem alterar nada}
                            {--remove-only : Remove as triggers que sobram e apenas informa as que faltam}
                            {--connection= : Executa apenas na conexão informada}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ajusta as triggers de auditoria ao estado esperado';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if (!config('audit.enabled', true)) {
            $this->info('Auditoria desativada nesta instalação: nada a fazer.');

            return self::SUCCESS;
        }

        $divergent = 0;
        $failed = 0;
        $connections = 0;

        $this->eachConnection(function (string $connection) use (&$divergent, &$failed, &$connections) {
            $connections++;

            if ($this->option('connection') && $this->option('connection') !== $connection) {
                return;
            }

            if (!$this->auditFunctionExists()) {
                if ($this->output->isVerbose()) {
                    $this->line("{$connection}: auditoria não instalada, ignorado.");
                }

                return;
            }

            try {
                $divergent += $this->reconcile($connection);
            } catch (Throwable $exception) {
                $failed++;

                $this->error("{$connection}: " . $exception->getMessage());

                report($exception);
            }
        });

        if ($connections === 0) {
            $this->warn('Nenhuma conexão encontrada: nada foi verificado.');

            return self::FAILURE;
        }

        if ($failed > 0) {
            $this->error("{$failed} conexão(ões) com falha. Reexecute o comando: a reconciliação é idempotente.");

            return self::FAILURE;
        }

        if ($divergent === 0) {
            $this->info('Auditoria consistente em todas as conexões.');

            return self::SUCCESS;
        }

        return $this->option('dry-run') ? self::FAILURE : self::SUCCESS;
    }

    private function reconcile(string $connection): int
    {
        $delta = $this->getAuditTriggersDelta();

        if ($delta === []) {
            return 0;
        }

        $toDrop = 0;
        $toCreate = 0;

        foreach ($delta as $table => $change) {
            $toDrop += count($change['drop']);
            $toCreate += (int) $change['create'];

            if ($this->output->isVerbose()) {
                $this->line("  {$table}: remover " . (implode(', ', $change['drop']) ?: 'nada')
                    . ($change['create'] ? ', criar trigger' : ''));
            }
        }

        $tables = count($delta);

        if ($this->option('dry-run')) {
            $this->warn("{$connection}: {$tables} tabela(s) divergente(s), {$toDrop} trigger(s) a remover e {$toCreate} a criar.");

            return $tables;
        }

        if ($this->option('remove-only')) {
            $result = $this->reconcileAuditTriggers(false);

            $this->info("{$connection}: {$result['dropped']} trigger(s) removida(s).");

            if ($toCreate > 0) {
                $this->warn("{$connection}: {$toCreate} tabela(s) sem auditoria aguardando decisão. Ver audit:reconcile --dry-run -v");
            }

            return $tables;
        }

        $result = $this->reconcileAuditTriggers();

        $this->info("{$connection}: {$result['dropped']} trigger(s) removida(s), {$result['created']} criada(s), em {$tables} tabela(s).");

        return $tables;
    }
}
