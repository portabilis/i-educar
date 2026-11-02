<?php

use App\Console\Commands\AuditReconcileCommand;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\Feature\Audit\AuditTestCase;

uses(AuditTestCase::class);

beforeEach(function () {
    $this->triggerName = function (string $table, ?string $connection = null): string {
        return DB::connection($connection)->scalar(
            "select tgname from pg_trigger where tgrelid = ?::regclass and tgfoid = 'public.audit()'::regprocedure and not tgisinternal;",
            [$table]
        );
    };
});

test('tabela nova aparece no delta e ganha trigger na reconciliacao', function () {
    $this->createTestTable();

    $delta = $this->getAuditTriggersDelta();

    $this->assertSame(['drop' => [], 'create' => true], $delta[self::TABELA] ?? null);

    $this->reconcileAuditTriggers();

    $this->assertSame(1, $this->countTableTriggers(self::TABELA));
    $this->assertSame('publicteste_auditoria_audit', ($this->triggerName)(self::TABELA));

    $records = $this->countAuditRecords('teste_auditoria', function () {
        DB::insert("insert into public.teste_auditoria (nome) values ('inicial');");
    });

    $this->assertSame(1, $records);
});

test('reconciliacao remove triggers de geracoes antigas', function () {
    $this->createTestTable();
    $this->createAuditTrigger(self::TABELA);

    DB::unprepared('create trigger teste_auditoria_audit after insert or update or delete on public.teste_auditoria for each row execute procedure public.audit();');
    DB::unprepared('create trigger nome_antigo_audit after insert or update or delete on public.teste_auditoria for each row execute procedure public.audit();');

    $this->assertSame(3, $this->countTableTriggers(self::TABELA));

    $delta = $this->getAuditTriggersDelta();

    $this->assertEqualsCanonicalizing(['teste_auditoria_audit', 'nome_antigo_audit'], $delta[self::TABELA]['drop']);
    $this->assertFalse($delta[self::TABELA]['create']);

    $this->reconcileAuditTriggers();

    $this->assertSame(1, $this->countTableTriggers(self::TABELA));
    $this->assertSame('publicteste_auditoria_audit', ($this->triggerName)(self::TABELA));
});

test('reconciliacao converge para delta vazio', function () {
    $this->reconcileAuditTriggers();

    $this->assertSame([], $this->getAuditTriggersDelta());

    foreach ($this->getAuditedTables() as $table) {
        $this->assertSame(1, $this->countTableTriggers($table), "A tabela {$table} deveria ter exatamente uma trigger");
    }
});

test('remover somente nao cria as triggers que faltam', function () {
    $this->createTestTable();

    DB::unprepared('create trigger sobra_audit after insert or update or delete on public.teste_auditoria for each row execute procedure public.audit();');

    $result = $this->reconcileAuditTriggers(false);

    $this->assertSame(0, $result['created']);
    $this->assertSame(0, $this->countTableTriggers(self::TABELA));
    $this->assertTrue($this->getAuditTriggersDelta()[self::TABELA]['create']);
});

test('tabela ignorada com trigger tem a trigger removida', function () {
    DB::unprepared($this->getSqlForCreateAuditTrigger('public.migrations'));

    $delta = $this->getAuditTriggersDelta();

    $this->assertSame(['drop' => ['publicmigrations_audit'], 'create' => false], $delta['public.migrations'] ?? null);

    $this->reconcileAuditTriggers();

    $this->assertSame(0, $this->countTableTriggers('public.migrations'));
});

/**
 * O critério de remoção é a função executada, nunca o nome: trigger de
 * outra função sobrevive à reconciliação mesmo terminando em _audit.
 */
test('troca de geracao preserva trigger de outra funcao', function () {
    $this->createTestTable();

    DB::unprepared('create function public.teste_auditoria_negocio() returns trigger language plpgsql as $$ begin return null; end; $$;');
    DB::unprepared('create trigger teste_auditoria_negocio_audit after insert on public.teste_auditoria for each row execute procedure public.teste_auditoria_negocio();');
    DB::unprepared('create trigger nome_antigo_audit after insert or update or delete on public.teste_auditoria for each row execute procedure public.audit();');

    $delta = $this->getAuditTriggersDelta();

    $this->assertSame(['drop' => ['nome_antigo_audit'], 'create' => true], $delta[self::TABELA] ?? null);

    $this->reconcileAuditTriggers();

    $this->assertSame('publicteste_auditoria_audit', ($this->triggerName)(self::TABELA));
    $this->assertTrue(DB::scalar(
        'select exists (select 1 from pg_trigger where tgrelid = ?::regclass and tgname = ?);',
        [self::TABELA, 'teste_auditoria_negocio_audit']
    ));
});

test('trigger com nome que exige aspas e removida', function () {
    $this->createTestTable();
    $this->createAuditTrigger(self::TABELA);

    DB::unprepared('create trigger "Velha_Audit" after insert on public.teste_auditoria for each row execute procedure public.audit();');
    DB::unprepared('create trigger "velha""estranha_audit" after insert on public.teste_auditoria for each row execute procedure public.audit();');

    $delta = $this->getAuditTriggersDelta();

    $this->assertEqualsCanonicalizing(['Velha_Audit', 'velha"estranha_audit'], $delta[self::TABELA]['drop']);

    $this->reconcileAuditTriggers();

    $this->assertSame(1, $this->countTableTriggers(self::TABELA));
    $this->assertSame('publicteste_auditoria_audit', ($this->triggerName)(self::TABELA));
});

/**
 * Auditoria instalada à mão em schema fora do sistema pertence a quem a
 * instalou: a reconciliação não a enxerga nem a destrói.
 */
test('trigger de auditoria em schema nao auditado nao e tocada', function () {
    DB::unprepared('create schema teste_auditoria_fora;');
    DB::unprepared('create table teste_auditoria_fora.alvo (id int);');
    DB::unprepared('create trigger alvo_audit after insert or update or delete on teste_auditoria_fora.alvo for each row execute procedure public.audit();');

    $this->assertNotContains('teste_auditoria_fora.alvo', $this->getAuditedTables());
    $this->assertArrayNotHasKey('teste_auditoria_fora.alvo', $this->getAuditTriggersDelta());

    $this->reconcileAuditTriggers();

    $this->assertSame(1, $this->countTableTriggers('teste_auditoria_fora.alvo'));
});

/**
 * Reverter a migration precisa deixar a auditoria funcional no formato da
 * geração anterior, que registra alteração mesmo sem mudança de valor.
 */
test('reverter a migration restaura as funcoes da geracao anterior', function () {
    $this->createTestTable();
    $this->createAuditTrigger(self::TABELA);

    (require base_path('database/migrations/audit/2026_08_14_000001_optimize_audit_functions.php'))->down();

    $records = $this->countAuditRecords('teste_auditoria', function () {
        DB::insert("insert into public.teste_auditoria (nome) values ('inicial');");
        DB::update('update public.teste_auditoria set nome = nome;');
    });

    $this->assertSame(2, $records);
});

test('comando encerra cedo quando a auditoria esta desativada', function () {
    config(['audit.enabled' => false]);

    $this->artisan('audit:reconcile')
        ->expectsOutput('Auditoria desativada nesta instalação: nada a fazer.')
        ->assertExitCode(0);
});

/**
 * O agendamento é o único mecanismo que converge as triggers, então tabela
 * criada por migration só passa a ser auditada na madrugada seguinte.
 */
test('reconciliacao completa esta agendada diariamente', function () {
    $scheduled = collect(app(Schedule::class)->events())
        ->filter(fn ($event) => str_contains((string) $event->command, 'audit:reconcile'));

    $this->assertCount(1, $scheduled);

    $event = $scheduled->first();

    $this->assertSame('30 3 * * *', $event->expression);
    $this->assertTrue(str_ends_with($event->command, 'audit:reconcile'), 'O agendamento deve executar a reconciliação completa, sem opções');
    $this->assertTrue($event->withoutOverlapping);
});

test('visao em schema auditado fica fora da reconciliacao', function () {
    $this->createTestTable();

    DB::unprepared('create view public.teste_auditoria_visao as select * from public.teste_auditoria;');

    $audited = $this->getAuditedTables();

    $this->assertContains(self::TABELA, $audited);
    $this->assertNotContains('public.teste_auditoria_visao', $audited);
    $this->assertArrayNotHasKey('public.teste_auditoria_visao', $this->getAuditTriggersDelta());
});

test('banco sem auditoria instalada nao e alterado', function () {
    DB::unprepared('drop function public.audit() cascade;');

    $this->assertFalse($this->auditFunctionExists());
    $this->assertSame([], $this->getAuditTriggersDelta());
    $this->assertSame(['dropped' => 0, 'created' => 0], $this->reconcileAuditTriggers());
});

test('migrations de auditoria aplicam e convergem', function () {
    Artisan::call('migrate', ['--path' => 'database/migrations/audit', '--force' => true]);

    $this->assertTrue(Schema::hasTable('ieducar_audit'));

    // Com o parâmetro vazio a função antiga abortava a escrita; a nova
    // precisa responder que a auditoria segue ligada
    DB::unprepared('set "audit.enabled" = \'\';');

    $this->assertTrue(DB::scalar('select public.audit_enabled();'));

    $this->reconcileAuditTriggers();

    $this->assertSame([], $this->getAuditTriggersDelta());
});

/**
 * Roda o comando de verdade contra um banco descartável próprio, o único
 * jeito de passar pelo percurso das conexões sem tocar no banco da suíte.
 */
test('comando reconcilia uma conexao do inicio ao fim', function () {
    $database = 'ieducar_teste_audit_reconcile';
    $default = config('database.default');
    $config = config("database.connections.{$default}");

    config(['database.connections.auditoria_admin' => array_merge($config, ['database' => 'postgres'])]);
    config(['database.connections.auditoria_alvo' => array_merge($config, ['database' => $database])]);

    DB::connection('auditoria_admin')->unprepared("drop database if exists {$database} with (force);");
    DB::connection('auditoria_admin')->unprepared("create database {$database};");

    try {
        DB::usingConnection('auditoria_alvo', function () {
            $this->installAuditFunctions();

            DB::unprepared('create table public.alvo (id int);');
            DB::unprepared('create trigger sobra_audit after insert or update or delete on public.alvo for each row execute procedure public.audit();');
        });

        $this->artisan('audit:reconcile', ['--remove-only' => true, '--connection' => 'auditoria_alvo'])->assertExitCode(0);

        $this->assertSame(0, $this->countTableTriggers('public.alvo', 'auditoria_alvo'));

        $this->artisan('audit:reconcile', ['--dry-run' => true, '--connection' => 'auditoria_alvo'])->assertExitCode(1);

        $this->artisan('audit:reconcile', ['--connection' => 'auditoria_alvo'])->assertExitCode(0);

        $this->assertSame(1, $this->countTableTriggers('public.alvo', 'auditoria_alvo'));
        $this->assertSame('publicalvo_audit', ($this->triggerName)('public.alvo', 'auditoria_alvo'));

        $this->artisan('audit:reconcile', ['--dry-run' => true, '--connection' => 'auditoria_alvo'])->assertExitCode(0);
    } finally {
        DB::setDefaultConnection($default);
        DB::purge('auditoria_alvo');
        DB::connection('auditoria_admin')->unprepared("drop database if exists {$database} with (force);");
        DB::purge('auditoria_admin');
    }
});

test('sem conexoes de clientes nada e verificado', function () {
    config(['database.connections' => ['pgsql' => config('database.connections.pgsql')]]);

    $this->artisan('audit:reconcile')
        ->expectsOutput('Nenhuma conexão encontrada: nada foi verificado.')
        ->assertExitCode(1);
});

test('banco de cliente sem auditoria instalada e ignorado', function () {
    $pgsql = config('database.connections.pgsql');
    config(['database.connections' => ['pgsql' => $pgsql, 'cliente_sem_auditoria' => array_merge($pgsql, ['database' => 'postgres'])]]);

    try {
        $this->artisan('audit:reconcile', ['-v' => true])
            ->expectsOutput('cliente_sem_auditoria: auditoria não instalada, ignorado.')
            ->assertExitCode(0);
    } finally {
        DB::setDefaultConnection('pgsql');
    }
});

test('detalha as diferencas e conta as conexoes que falham', function () {
    $pgsql = config('database.connections.pgsql');
    config(['database.connections' => ['pgsql' => $pgsql, 'cliente' => $pgsql]]);

    // A reconciliação usa a conexão do cliente como padrão; o delta vem pronto
    // para não alterar o banco da suíte.
    $this->app->bind(AuditReconcileCommand::class, fn () => new class extends AuditReconcileCommand
    {
        public static ?Throwable $falha = null;

        public function getAuditTriggersDelta(): array
        {
            if (self::$falha) {
                throw self::$falha;
            }

            return ['public.alvo' => ['drop' => ['sobra_audit'], 'create' => true]];
        }
    });

    try {
        $this->artisan('audit:reconcile', ['--dry-run' => true, '-v' => true])
            ->expectsOutput('  public.alvo: remover sobra_audit, criar trigger')
            ->assertExitCode(1);

        $command = $this->app->make(AuditReconcileCommand::class);
        $command::$falha = new RuntimeException('sem permissão');

        $this->artisan('audit:reconcile')
            ->expectsOutput('cliente: sem permissão')
            ->expectsOutput('1 conexão(ões) com falha. Reexecute o comando: a reconciliação é idempotente.')
            ->assertExitCode(1);
    } finally {
        DB::setDefaultConnection('pgsql');
    }
});
