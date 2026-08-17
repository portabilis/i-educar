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

        $divergentes = 0;
        $falhas = 0;

        $this->eachConnection(function (string $connection) use (&$divergentes, &$falhas) {
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
                $divergentes += $this->reconcile($connection);
            } catch (Throwable $erro) {
                // Falha em um município, como lock não obtido, não pode impedir os demais
                $falhas++;

                $this->error("{$connection}: " . $erro->getMessage());

                report($erro);
            }
        });

        if ($falhas > 0) {
            $this->error("{$falhas} conexão(ões) com falha. Reexecute o comando: a reconciliação é idempotente.");

            return self::FAILURE;
        }

        if ($divergentes === 0) {
            $this->info('Auditoria consistente em todas as conexões.');

            return self::SUCCESS;
        }

        // Saída diferente de zero permite que a integração contínua reprove a alteração
        return $this->option('dry-run') ? self::FAILURE : self::SUCCESS;
    }

    /**
     * Informa ou aplica as diferenças da conexão atual.
     *
     * @return int Quantidade de tabelas divergentes
     */
    private function reconcile(string $connection): int
    {
        $delta = $this->getAuditTriggersDelta();

        if ($delta === []) {
            return 0;
        }

        $remover = 0;
        $criar = 0;

        foreach ($delta as $tabela => $diferenca) {
            $remover += count($diferenca['drop']);
            $criar += (int) $diferenca['create'];

            if ($this->output->isVerbose()) {
                $this->line("  {$tabela}: remover " . (implode(', ', $diferenca['drop']) ?: 'nada')
                    . ($diferenca['create'] ? ', criar trigger' : ''));
            }
        }

        $tabelas = count($delta);

        if ($this->option('dry-run')) {
            $this->warn("{$connection}: {$tabelas} tabela(s) divergente(s), {$remover} trigger(s) a remover e {$criar} a criar.");

            return $tabelas;
        }

        if ($this->option('remove-only')) {
            $resultado = $this->reconcileAuditTriggers(false);

            $this->info("{$connection}: {$resultado['dropped']} trigger(s) removida(s).");

            if ($criar > 0) {
                $this->warn("{$connection}: {$criar} tabela(s) sem auditoria aguardando decisão. Ver audit:reconcile --dry-run -v");
            }

            return $tabelas;
        }

        $resultado = $this->reconcileAuditTriggers();

        $this->info("{$connection}: {$resultado['dropped']} trigger(s) removida(s), {$resultado['created']} criada(s), em {$tabelas} tabela(s).");

        return $tabelas;
    }
}
