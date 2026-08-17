<?php

namespace Tests\Feature\Audit;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AuditReconcileTest extends AuditTestCase
{
    public function test_tabela_nova_aparece_no_delta_e_ganha_trigger_na_reconciliacao(): void
    {
        $this->criarTabelaDeTeste();

        $delta = $this->getAuditTriggersDelta();

        $this->assertSame(['drop' => [], 'create' => true], $delta[self::TABELA] ?? null);

        $this->reconcileAuditTriggers();

        $this->assertSame(1, $this->contarTriggersDaTabela(self::TABELA));
        $this->assertSame('publicteste_auditoria_audit', $this->nomeDaTrigger(self::TABELA));

        $registros = $this->contarRegistrosDeAuditoria('teste_auditoria', function () {
            DB::insert("insert into public.teste_auditoria (nome) values ('inicial');");
        });

        $this->assertSame(1, $registros);
    }

    public function test_reconciliacao_remove_triggers_de_geracoes_antigas(): void
    {
        $this->criarTabelaDeTeste();
        $this->createAuditTrigger(self::TABELA);

        DB::unprepared('create trigger teste_auditoria_audit after insert or update or delete on public.teste_auditoria for each row execute procedure public.audit();');
        DB::unprepared('create trigger nome_antigo_audit after insert or update or delete on public.teste_auditoria for each row execute procedure public.audit();');

        $this->assertSame(3, $this->contarTriggersDaTabela(self::TABELA));

        $delta = $this->getAuditTriggersDelta();

        $this->assertEqualsCanonicalizing(['teste_auditoria_audit', 'nome_antigo_audit'], $delta[self::TABELA]['drop']);
        $this->assertFalse($delta[self::TABELA]['create']);

        $this->reconcileAuditTriggers();

        $this->assertSame(1, $this->contarTriggersDaTabela(self::TABELA));
        $this->assertSame('publicteste_auditoria_audit', $this->nomeDaTrigger(self::TABELA));
    }

    public function test_reconciliacao_converge_para_delta_vazio(): void
    {
        $this->reconcileAuditTriggers();

        $this->assertSame([], $this->getAuditTriggersDelta());

        foreach ($this->getAuditedTables() as $tabela) {
            $this->assertSame(1, $this->contarTriggersDaTabela($tabela), "A tabela {$tabela} deveria ter exatamente uma trigger");
        }
    }

    public function test_remover_somente_nao_cria_as_triggers_que_faltam(): void
    {
        $this->criarTabelaDeTeste();

        DB::unprepared('create trigger sobra_audit after insert or update or delete on public.teste_auditoria for each row execute procedure public.audit();');

        $resultado = $this->reconcileAuditTriggers(false);

        $this->assertSame(0, $resultado['created']);
        $this->assertSame(0, $this->contarTriggersDaTabela(self::TABELA));
        $this->assertTrue($this->getAuditTriggersDelta()[self::TABELA]['create']);
    }

    public function test_tabela_ignorada_com_trigger_tem_a_trigger_removida(): void
    {
        DB::unprepared($this->getSqlForCreateAuditTrigger('public.migrations'));

        $delta = $this->getAuditTriggersDelta();

        $this->assertSame(['drop' => ['publicmigrations_audit'], 'create' => false], $delta['public.migrations'] ?? null);

        $this->reconcileAuditTriggers();

        $this->assertSame(0, $this->contarTriggersDaTabela('public.migrations'));
    }

    /**
     * O critério de remoção é a função executada, nunca o nome: trigger de
     * outra função sobrevive à reconciliação mesmo terminando em _audit.
     */
    public function test_troca_de_geracao_preserva_trigger_de_outra_funcao(): void
    {
        $this->criarTabelaDeTeste();

        DB::unprepared('create function public.teste_auditoria_negocio() returns trigger language plpgsql as $$ begin return null; end; $$;');
        DB::unprepared('create trigger teste_auditoria_negocio_audit after insert on public.teste_auditoria for each row execute procedure public.teste_auditoria_negocio();');
        DB::unprepared('create trigger nome_antigo_audit after insert or update or delete on public.teste_auditoria for each row execute procedure public.audit();');

        $delta = $this->getAuditTriggersDelta();

        $this->assertSame(['drop' => ['nome_antigo_audit'], 'create' => true], $delta[self::TABELA] ?? null);

        $this->reconcileAuditTriggers();

        $this->assertSame('publicteste_auditoria_audit', $this->nomeDaTrigger(self::TABELA));
        $this->assertTrue(DB::scalar(
            'select exists (select 1 from pg_trigger where tgrelid = ?::regclass and tgname = ?);',
            [self::TABELA, 'teste_auditoria_negocio_audit']
        ));
    }

    public function test_trigger_com_nome_que_exige_aspas_e_removida(): void
    {
        $this->criarTabelaDeTeste();
        $this->createAuditTrigger(self::TABELA);

        DB::unprepared('create trigger "Velha_Audit" after insert on public.teste_auditoria for each row execute procedure public.audit();');
        DB::unprepared('create trigger "velha""estranha_audit" after insert on public.teste_auditoria for each row execute procedure public.audit();');

        $delta = $this->getAuditTriggersDelta();

        $this->assertEqualsCanonicalizing(['Velha_Audit', 'velha"estranha_audit'], $delta[self::TABELA]['drop']);

        $this->reconcileAuditTriggers();

        $this->assertSame(1, $this->contarTriggersDaTabela(self::TABELA));
        $this->assertSame('publicteste_auditoria_audit', $this->nomeDaTrigger(self::TABELA));
    }

    /**
     * Auditoria instalada à mão em schema fora do sistema pertence a quem a
     * instalou: a reconciliação não a enxerga nem a destrói.
     */
    public function test_trigger_de_auditoria_em_schema_nao_auditado_nao_e_tocada(): void
    {
        DB::unprepared('create schema teste_auditoria_fora;');
        DB::unprepared('create table teste_auditoria_fora.alvo (id int);');
        DB::unprepared('create trigger alvo_audit after insert or update or delete on teste_auditoria_fora.alvo for each row execute procedure public.audit();');

        $this->assertNotContains('teste_auditoria_fora.alvo', $this->getAuditedTables());
        $this->assertArrayNotHasKey('teste_auditoria_fora.alvo', $this->getAuditTriggersDelta());

        $this->reconcileAuditTriggers();

        $this->assertSame(1, $this->contarTriggersDaTabela('teste_auditoria_fora.alvo'));
    }

    /**
     * Reverter a migration precisa deixar a auditoria funcional no formato da
     * geração anterior, que registra alteração mesmo sem mudança de valor.
     */
    public function test_reverter_a_migration_restaura_as_funcoes_da_geracao_anterior(): void
    {
        $this->criarTabelaDeTeste();
        $this->createAuditTrigger(self::TABELA);

        (require base_path('database/migrations/audit/2026_08_14_000001_optimize_audit_functions.php'))->down();

        $registros = $this->contarRegistrosDeAuditoria('teste_auditoria', function () {
            DB::insert("insert into public.teste_auditoria (nome) values ('inicial');");
            DB::update('update public.teste_auditoria set nome = nome;');
        });

        $this->assertSame(2, $registros);
    }

    public function test_comando_encerra_cedo_quando_a_auditoria_esta_desativada(): void
    {
        config(['audit.enabled' => false]);

        $this->artisan('audit:reconcile')
            ->expectsOutput('Auditoria desativada nesta instalação: nada a fazer.')
            ->assertExitCode(0);
    }

    /**
     * O agendamento é o único mecanismo que converge as triggers, então tabela
     * criada por migration só passa a ser auditada na madrugada seguinte.
     */
    public function test_reconciliacao_completa_esta_agendada_diariamente(): void
    {
        $agendados = collect(app(Schedule::class)->events())
            ->filter(fn ($evento) => str_contains((string) $evento->command, 'audit:reconcile'));

        $this->assertCount(1, $agendados);

        $evento = $agendados->first();

        $this->assertSame('30 3 * * *', $evento->expression);
        $this->assertTrue(str_ends_with($evento->command, 'audit:reconcile'), 'O agendamento deve executar a reconciliação completa, sem opções');
        $this->assertTrue($evento->withoutOverlapping);
    }

    public function test_visao_em_schema_auditado_fica_fora_da_reconciliacao(): void
    {
        $this->criarTabelaDeTeste();

        DB::unprepared('create view public.teste_auditoria_visao as select * from public.teste_auditoria;');

        $auditadas = $this->getAuditedTables();

        $this->assertContains(self::TABELA, $auditadas);
        $this->assertNotContains('public.teste_auditoria_visao', $auditadas);
        $this->assertArrayNotHasKey('public.teste_auditoria_visao', $this->getAuditTriggersDelta());
    }

    public function test_banco_sem_auditoria_instalada_nao_e_alterado(): void
    {
        DB::unprepared('drop function public.audit() cascade;');

        $this->assertFalse($this->auditFunctionExists());
        $this->assertSame([], $this->getAuditTriggersDelta());
        $this->assertSame(['dropped' => 0, 'created' => 0], $this->reconcileAuditTriggers());
    }

    public function test_migrations_de_auditoria_aplicam_e_convergem(): void
    {
        Artisan::call('migrate', ['--path' => 'database/migrations/audit', '--force' => true]);

        $this->assertTrue(Schema::hasTable('ieducar_audit'));

        // Com o parâmetro vazio a função antiga abortava a escrita; a nova
        // precisa responder que a auditoria segue ligada
        DB::unprepared('set "audit.enabled" = \'\';');

        $this->assertTrue(DB::scalar('select public.audit_enabled();'));

        $this->reconcileAuditTriggers();

        $this->assertSame([], $this->getAuditTriggersDelta());
    }

    /**
     * Roda o comando de verdade contra um banco descartável próprio, o único
     * jeito de passar pelo percurso das conexões sem tocar no banco da suíte.
     */
    public function test_comando_reconcilia_uma_conexao_do_inicio_ao_fim(): void
    {
        $banco = 'ieducar_teste_audit_reconcile';
        $padrao = config('database.default');
        $config = config("database.connections.{$padrao}");

        config(['database.connections.auditoria_admin' => array_merge($config, ['database' => 'postgres'])]);
        config(['database.connections.auditoria_alvo' => array_merge($config, ['database' => $banco])]);

        DB::connection('auditoria_admin')->unprepared("drop database if exists {$banco} with (force);");
        DB::connection('auditoria_admin')->unprepared("create database {$banco};");

        try {
            DB::usingConnection('auditoria_alvo', function () {
                $this->instalarFuncoesDeAuditoria();

                DB::unprepared('create table public.alvo (id int);');
                DB::unprepared('create trigger sobra_audit after insert or update or delete on public.alvo for each row execute procedure public.audit();');
            });

            $this->artisan('audit:reconcile', ['--remove-only' => true, '--connection' => 'auditoria_alvo'])->assertExitCode(0);

            $this->assertSame(0, $this->contarTriggersDaTabela('public.alvo', 'auditoria_alvo'));

            $this->artisan('audit:reconcile', ['--dry-run' => true, '--connection' => 'auditoria_alvo'])->assertExitCode(1);

            $this->artisan('audit:reconcile', ['--connection' => 'auditoria_alvo'])->assertExitCode(0);

            $this->assertSame(1, $this->contarTriggersDaTabela('public.alvo', 'auditoria_alvo'));
            $this->assertSame('publicalvo_audit', $this->nomeDaTrigger('public.alvo', 'auditoria_alvo'));

            $this->artisan('audit:reconcile', ['--dry-run' => true, '--connection' => 'auditoria_alvo'])->assertExitCode(0);
        } finally {
            DB::setDefaultConnection($padrao);
            DB::purge('auditoria_alvo');
            DB::connection('auditoria_admin')->unprepared("drop database if exists {$banco} with (force);");
            DB::purge('auditoria_admin');
        }
    }

    private function nomeDaTrigger(string $tabela, ?string $conexao = null): string
    {
        return DB::connection($conexao)->scalar(
            "select tgname from pg_trigger where tgrelid = ?::regclass and tgfoid = 'public.audit()'::regprocedure and not tgisinternal;",
            [$tabela]
        );
    }
}
