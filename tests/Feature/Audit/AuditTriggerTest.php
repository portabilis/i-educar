<?php

use Illuminate\Support\Facades\DB;
use Tests\Feature\Audit\AuditTestCase;

uses(AuditTestCase::class);

beforeEach(function () {
    $this->createTestTable();
    $this->createAuditTrigger(self::TABELA);
});

test('lista de tabelas ignoradas usa apenas nome qualificado', function () {
    foreach ($this->getSkippedTables() as $entry) {
        $schema = explode('.', $entry)[0];

        $this->assertStringContainsString('.', $entry, "A entrada {$entry} precisa do schema");
        $this->assertContains($schema, ['cadastro', 'modules', 'pmieducar', 'portal', 'public', 'relatorio'], "O schema de {$entry} não é auditado");
    }
});

test('lista de tabelas ignoradas nao tem entrada repetida', function () {
    $list = $this->getSkippedTables();

    $this->assertSame(array_unique($list), $list, 'Há entrada repetida na lista');
});

test('tabela ignorada fica fora das auditadas', function () {
    $audited = $this->getAuditedTables();

    foreach ($this->getSkippedTables() as $entry) {
        $this->assertNotContains($entry, $audited);
    }

    // Fixado fora da lista: auditar a própria tabela de auditoria faria a
    // trigger disparar a si mesma em recursão infinita
    $this->assertNotContains('public.ieducar_audit', $audited);

    $this->assertContains('pmieducar.matricula', $audited);
    $this->assertContains('cadastro.fisica', $audited);
});

/**
 * Instalação com lista própria herdada de gerações anteriores usa nome sem
 * schema, e esse formato precisa continuar surtindo efeito.
 */
test('lista de ignoradas aceita nome sem schema', function () {
    config(['audit.skip' => ['migrations']]);

    $this->assertNotContains('public.migrations', $this->getAuditedTables());
});

test('insercao e registrada sem estado anterior', function () {
    $records = $this->countAuditRecords('teste_auditoria', function () {
        DB::insert("insert into public.teste_auditoria (nome) values ('inicial');");
    });

    $this->assertSame(1, $records);

    $record = $this->lastAuditRecord('teste_auditoria');

    $this->assertSame('public', $record->schema);
    $this->assertNull($record->before);
    $this->assertSame('inicial', $record->after->nome);
    $this->assertSame(0, $record->context->user_id);
    $this->assertNotEmpty($record->context->user_name);
});

test('alteracao e registrada com antes e depois', function () {
    DB::insert("insert into public.teste_auditoria (nome) values ('inicial');");

    $records = $this->countAuditRecords('teste_auditoria', function () {
        DB::update("update public.teste_auditoria set nome = 'alterado';");
    });

    $this->assertSame(1, $records);

    $record = $this->lastAuditRecord('teste_auditoria');

    $this->assertSame('inicial', $record->before->nome);
    $this->assertSame('alterado', $record->after->nome);
});

test('exclusao e registrada sem estado posterior', function () {
    DB::insert("insert into public.teste_auditoria (nome) values ('inicial');");

    $records = $this->countAuditRecords('teste_auditoria', function () {
        DB::delete('delete from public.teste_auditoria;');
    });

    $this->assertSame(1, $records);

    $record = $this->lastAuditRecord('teste_auditoria');

    $this->assertSame('inicial', $record->before->nome);
    $this->assertNull($record->after);
});

test('alteracao sem mudanca nao e registrada', function () {
    DB::insert("insert into public.teste_auditoria (nome) values ('inicial');");

    $records = $this->countAuditRecords('teste_auditoria', function () {
        DB::update('update public.teste_auditoria set nome = nome;');
    });

    $this->assertSame(0, $records);
});

test('alteracao apenas de carimbo nao e registrada', function () {
    DB::insert("insert into public.teste_auditoria (nome, updated_at) values ('inicial', now());");

    $records = $this->countAuditRecords('teste_auditoria', function () {
        DB::update("update public.teste_auditoria set updated_at = now() + interval '1 hour';");
    });

    $this->assertSame(0, $records);
});

test('alteracao de carimbo junto com dado e registrada', function () {
    DB::insert("insert into public.teste_auditoria (nome, updated_at) values ('inicial', now());");

    $records = $this->countAuditRecords('teste_auditoria', function () {
        DB::update("update public.teste_auditoria set nome = 'alterado', updated_at = now() + interval '1 hour';");
    });

    $this->assertSame(1, $records);

    $record = $this->lastAuditRecord('teste_auditoria');

    $this->assertSame('alterado', $record->after->nome);
    $this->assertNotSame($record->before->updated_at, $record->after->updated_at);
});

test('alteracao em lote registra somente as linhas que mudaram', function () {
    DB::insert("insert into public.teste_auditoria (nome) values ('um'), ('dois'), ('alterado');");

    $records = $this->countAuditRecords('teste_auditoria', function () {
        DB::update("update public.teste_auditoria set nome = 'alterado';");
    });

    $this->assertSame(2, $records);
});

test('tabela com coluna json nao impede a comparacao', function () {
    DB::insert('insert into public.teste_auditoria (nome, dados) values (\'inicial\', \'{"a": 1}\');');

    $records = $this->countAuditRecords('teste_auditoria', function () {
        DB::update('update public.teste_auditoria set dados = \'{"a": 2}\';');
    });

    $this->assertSame(1, $records);
});

test('mudanca minima de float e registrada', function () {
    DB::insert('insert into public.teste_auditoria (nome, valor) values (\'inicial\', 1.0);');

    $records = $this->countAuditRecords('teste_auditoria', function () {
        DB::update('update public.teste_auditoria set valor = 1.0000000000000002;');
    });

    $this->assertSame(1, $records);
});

/**
 * Limitação aceita: com extra_float_digits negativo na sessão, floats
 * diferentes imprimem o mesmo texto e a comparação textual considera a
 * linha inalterada. Convertê-la para jsonb não resolveria, o valor
 * colapsa igual. Nenhum driver usado pelo sistema rebaixa esse parâmetro.
 */
test('extra float digits negativo engole mudanca de float', function () {
    DB::insert('insert into public.teste_auditoria (nome, valor) values (\'inicial\', 1.0);');

    $records = $this->countAuditRecords('teste_auditoria', function () {
        DB::unprepared('set local extra_float_digits = -15; update public.teste_auditoria set valor = 1.0000000000000002;');
    });

    $this->assertSame(0, $records);
    $this->assertTrue(DB::scalar('select valor <> 1.0 from public.teste_auditoria;'));
});

test('tabela sem chave primaria e auditada', function () {
    DB::unprepared('create table public.teste_auditoria_sem_pk (nome varchar(255));');
    $this->createAuditTrigger('public.teste_auditoria_sem_pk');

    $records = $this->countAuditRecords('teste_auditoria_sem_pk', function () {
        DB::insert("insert into public.teste_auditoria_sem_pk values ('inicial');");
        DB::update("update public.teste_auditoria_sem_pk set nome = 'alterado';");
        DB::delete('delete from public.teste_auditoria_sem_pk;');
    });

    $this->assertSame(3, $records);
});

test('tabela com coluna gerada e auditada', function () {
    DB::unprepared('create table public.teste_auditoria_gerada (id int primary key, base int, dobro int generated always as (base * 2) stored);');
    $this->createAuditTrigger('public.teste_auditoria_gerada');

    DB::insert('insert into public.teste_auditoria_gerada (id, base) values (1, 2);');

    $records = $this->countAuditRecords('teste_auditoria_gerada', function () {
        DB::update('update public.teste_auditoria_gerada set base = 3;');
    });

    $this->assertSame(1, $records);
    $this->assertSame(6, $this->lastAuditRecord('teste_auditoria_gerada')->after->dobro);

    $records = $this->countAuditRecords('teste_auditoria_gerada', function () {
        DB::update('update public.teste_auditoria_gerada set base = base;');
    });

    $this->assertSame(0, $records);
});

/**
 * A auditoria registra a linha como foi efetivamente gravada, depois das
 * triggers BEFORE da tabela; mudança desfeita por uma BEFORE não conta.
 */
test('linha alterada por trigger before e registrada com o valor final', function () {
    DB::unprepared('create function public.teste_auditoria_before() returns trigger language plpgsql as $$ begin new.nome := upper(new.nome); return new; end; $$;');
    DB::unprepared('create trigger teste_auditoria_before before update on public.teste_auditoria for each row execute procedure public.teste_auditoria_before();');

    DB::insert("insert into public.teste_auditoria (nome) values ('inicial');");

    $records = $this->countAuditRecords('teste_auditoria', function () {
        DB::update("update public.teste_auditoria set nome = 'alterado';");
    });

    $this->assertSame(1, $records);
    $this->assertSame('ALTERADO', $this->lastAuditRecord('teste_auditoria')->after->nome);

    DB::unprepared('create or replace function public.teste_auditoria_before() returns trigger language plpgsql as $$ begin new.nome := old.nome; return new; end; $$;');

    $records = $this->countAuditRecords('teste_auditoria', function () {
        DB::update("update public.teste_auditoria set nome = 'descartado';");
    });

    $this->assertSame(0, $records);
});

/**
 * Limitação aceita, igual à das gerações anteriores: a trigger é por linha
 * e o TRUNCATE não dispara trigger por linha, então esvazia a tabela sem
 * deixar rastro na auditoria.
 */
test('truncate esvazia a tabela sem registro', function () {
    DB::insert("insert into public.teste_auditoria (nome) values ('um'), ('dois');");

    $records = $this->countAuditRecords('teste_auditoria', function () {
        DB::unprepared('truncate public.teste_auditoria;');
    });

    $this->assertSame(0, $records);
    $this->assertSame(0, DB::scalar('select count(*) from public.teste_auditoria;'));
});

/**
 * O RESET deixa o parâmetro de sessão com string vazia, e não com o valor
 * original. A escrita não pode abortar nesse estado e a auditoria deve
 * seguir ligada, que é o comportamento padrão.
 */
test('desligar e religar auditoria pela sessao', function () {
    DB::insert("insert into public.teste_auditoria (nome) values ('inicial');");

    DB::unprepared('set "audit.enabled" = \'false\';');

    $records = $this->countAuditRecords('teste_auditoria', function () {
        DB::update("update public.teste_auditoria set nome = 'sem auditoria';");
    });

    $this->assertSame(0, $records);

    DB::unprepared('reset "audit.enabled";');

    $this->assertSame('', DB::scalar("select current_setting('audit.enabled', true);"));

    $records = $this->countAuditRecords('teste_auditoria', function () {
        DB::update("update public.teste_auditoria set nome = 'com auditoria';");
    });

    $this->assertSame(1, $records);
});

test('valor invalido no parametro nao interrompe a alteracao', function () {
    DB::insert("insert into public.teste_auditoria (nome) values ('inicial');");

    DB::unprepared('set "audit.enabled" = \'invalido\';');

    $records = $this->countAuditRecords('teste_auditoria', function () {
        DB::update("update public.teste_auditoria set nome = 'alterado';");
    });

    $this->assertSame(1, $records);
});

test('contexto invalido usa o contexto padrao', function () {
    DB::insert("insert into public.teste_auditoria (nome) values ('inicial');");

    DB::unprepared('set "audit.context" = \'{quebrado\';');

    $records = $this->countAuditRecords('teste_auditoria', function () {
        DB::update("update public.teste_auditoria set nome = 'alterado';");
    });

    $this->assertSame(1, $records);
    $this->assertSame(0, $this->lastAuditRecord('teste_auditoria')->context->user_id);
});

test('contexto da sessao e gravado no registro', function () {
    DB::insert("insert into public.teste_auditoria (nome) values ('inicial');");

    DB::unprepared('set "audit.context" = \'{"user_id": 123, "user_name": "Fulano", "origin": "http://localhost/teste"}\';');

    DB::update("update public.teste_auditoria set nome = 'alterado';");

    $context = $this->lastAuditRecord('teste_auditoria')->context;

    $this->assertSame(123, $context->user_id);
    $this->assertSame('Fulano', $context->user_name);
    $this->assertSame('http://localhost/teste', $context->origin);
});
