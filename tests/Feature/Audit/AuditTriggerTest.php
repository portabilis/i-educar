<?php

namespace Tests\Feature\Audit;

use Illuminate\Support\Facades\DB;

class AuditTriggerTest extends AuditTestCase
{
    private const SCHEMAS = ['cadastro', 'modules', 'pmieducar', 'portal', 'public', 'relatorio'];

    protected function setUp(): void
    {
        parent::setUp();

        $this->criarTabelaDeTeste();
        $this->createAuditTrigger(self::TABELA);
    }

    public function test_lista_de_tabelas_ignoradas_usa_apenas_nome_qualificado(): void
    {
        foreach ($this->getSkippedTables() as $entrada) {
            $schema = explode('.', $entrada)[0];

            $this->assertStringContainsString('.', $entrada, "A entrada {$entrada} precisa do schema");
            $this->assertContains($schema, self::SCHEMAS, "O schema de {$entrada} não é auditado");
        }
    }

    public function test_lista_de_tabelas_ignoradas_nao_tem_entrada_repetida(): void
    {
        $lista = $this->getSkippedTables();

        $this->assertSame(array_unique($lista), $lista, 'Há entrada repetida na lista');
    }

    public function test_tabela_ignorada_fica_fora_das_auditadas(): void
    {
        $auditadas = $this->getAuditedTables();

        foreach ($this->getSkippedTables() as $entrada) {
            $this->assertNotContains($entrada, $auditadas);
        }

        // Fixado fora da lista: auditar a própria tabela de auditoria faria a
        // trigger disparar a si mesma em recursão infinita
        $this->assertNotContains('public.ieducar_audit', $auditadas);

        $this->assertContains('pmieducar.matricula', $auditadas);
        $this->assertContains('cadastro.fisica', $auditadas);
    }

    /**
     * Instalação com lista própria herdada de gerações anteriores usa nome sem
     * schema, e esse formato precisa continuar surtindo efeito.
     */
    public function test_lista_de_ignoradas_aceita_nome_sem_schema(): void
    {
        config(['audit.skip' => ['migrations']]);

        $this->assertNotContains('public.migrations', $this->getAuditedTables());
    }

    public function test_insercao_e_registrada_sem_estado_anterior(): void
    {
        $registros = $this->contarRegistrosDeAuditoria('teste_auditoria', function () {
            DB::insert("insert into public.teste_auditoria (nome) values ('inicial');");
        });

        $this->assertSame(1, $registros);

        $registro = $this->ultimoRegistroDeAuditoria('teste_auditoria');

        $this->assertSame('public', $registro->schema);
        $this->assertNull($registro->before);
        $this->assertSame('inicial', $registro->after->nome);
        $this->assertSame(0, $registro->context->user_id);
        $this->assertNotEmpty($registro->context->user_name);
    }

    public function test_alteracao_e_registrada_com_antes_e_depois(): void
    {
        DB::insert("insert into public.teste_auditoria (nome) values ('inicial');");

        $registros = $this->contarRegistrosDeAuditoria('teste_auditoria', function () {
            DB::update("update public.teste_auditoria set nome = 'alterado';");
        });

        $this->assertSame(1, $registros);

        $registro = $this->ultimoRegistroDeAuditoria('teste_auditoria');

        $this->assertSame('inicial', $registro->before->nome);
        $this->assertSame('alterado', $registro->after->nome);
    }

    public function test_exclusao_e_registrada_sem_estado_posterior(): void
    {
        DB::insert("insert into public.teste_auditoria (nome) values ('inicial');");

        $registros = $this->contarRegistrosDeAuditoria('teste_auditoria', function () {
            DB::delete('delete from public.teste_auditoria;');
        });

        $this->assertSame(1, $registros);

        $registro = $this->ultimoRegistroDeAuditoria('teste_auditoria');

        $this->assertSame('inicial', $registro->before->nome);
        $this->assertNull($registro->after);
    }

    public function test_alteracao_sem_mudanca_nao_e_registrada(): void
    {
        DB::insert("insert into public.teste_auditoria (nome) values ('inicial');");

        $registros = $this->contarRegistrosDeAuditoria('teste_auditoria', function () {
            DB::update('update public.teste_auditoria set nome = nome;');
        });

        $this->assertSame(0, $registros);
    }

    public function test_alteracao_apenas_de_carimbo_nao_e_registrada(): void
    {
        DB::insert("insert into public.teste_auditoria (nome, updated_at) values ('inicial', now());");

        $registros = $this->contarRegistrosDeAuditoria('teste_auditoria', function () {
            DB::update("update public.teste_auditoria set updated_at = now() + interval '1 hour';");
        });

        $this->assertSame(0, $registros);
    }

    public function test_alteracao_de_carimbo_junto_com_dado_e_registrada(): void
    {
        DB::insert("insert into public.teste_auditoria (nome, updated_at) values ('inicial', now());");

        $registros = $this->contarRegistrosDeAuditoria('teste_auditoria', function () {
            DB::update("update public.teste_auditoria set nome = 'alterado', updated_at = now() + interval '1 hour';");
        });

        $this->assertSame(1, $registros);

        $registro = $this->ultimoRegistroDeAuditoria('teste_auditoria');

        $this->assertSame('alterado', $registro->after->nome);
        $this->assertNotSame($registro->before->updated_at, $registro->after->updated_at);
    }

    public function test_alteracao_em_lote_registra_somente_as_linhas_que_mudaram(): void
    {
        DB::insert("insert into public.teste_auditoria (nome) values ('um'), ('dois'), ('alterado');");

        $registros = $this->contarRegistrosDeAuditoria('teste_auditoria', function () {
            DB::update("update public.teste_auditoria set nome = 'alterado';");
        });

        $this->assertSame(2, $registros);
    }

    public function test_tabela_com_coluna_json_nao_impede_a_comparacao(): void
    {
        DB::insert('insert into public.teste_auditoria (nome, dados) values (\'inicial\', \'{"a": 1}\');');

        $registros = $this->contarRegistrosDeAuditoria('teste_auditoria', function () {
            DB::update('update public.teste_auditoria set dados = \'{"a": 2}\';');
        });

        $this->assertSame(1, $registros);
    }

    public function test_mudanca_minima_de_float_e_registrada(): void
    {
        DB::insert('insert into public.teste_auditoria (nome, valor) values (\'inicial\', 1.0);');

        $registros = $this->contarRegistrosDeAuditoria('teste_auditoria', function () {
            DB::update('update public.teste_auditoria set valor = 1.0000000000000002;');
        });

        $this->assertSame(1, $registros);
    }

    /**
     * Limitação aceita: com extra_float_digits negativo na sessão, floats
     * diferentes imprimem o mesmo texto e a comparação textual considera a
     * linha inalterada. Convertê-la para jsonb não resolveria, o valor
     * colapsa igual. Nenhum driver usado pelo sistema rebaixa esse parâmetro.
     */
    public function test_extra_float_digits_negativo_engole_mudanca_de_float(): void
    {
        DB::insert('insert into public.teste_auditoria (nome, valor) values (\'inicial\', 1.0);');

        $registros = $this->contarRegistrosDeAuditoria('teste_auditoria', function () {
            DB::unprepared('set local extra_float_digits = -15; update public.teste_auditoria set valor = 1.0000000000000002;');
        });

        $this->assertSame(0, $registros);
        $this->assertTrue(DB::scalar('select valor <> 1.0 from public.teste_auditoria;'));
    }

    public function test_tabela_sem_chave_primaria_e_auditada(): void
    {
        DB::unprepared('create table public.teste_auditoria_sem_pk (nome varchar(255));');
        $this->createAuditTrigger('public.teste_auditoria_sem_pk');

        $registros = $this->contarRegistrosDeAuditoria('teste_auditoria_sem_pk', function () {
            DB::insert("insert into public.teste_auditoria_sem_pk values ('inicial');");
            DB::update("update public.teste_auditoria_sem_pk set nome = 'alterado';");
            DB::delete('delete from public.teste_auditoria_sem_pk;');
        });

        $this->assertSame(3, $registros);
    }

    public function test_tabela_com_coluna_gerada_e_auditada(): void
    {
        DB::unprepared('create table public.teste_auditoria_gerada (id int primary key, base int, dobro int generated always as (base * 2) stored);');
        $this->createAuditTrigger('public.teste_auditoria_gerada');

        DB::insert('insert into public.teste_auditoria_gerada (id, base) values (1, 2);');

        $registros = $this->contarRegistrosDeAuditoria('teste_auditoria_gerada', function () {
            DB::update('update public.teste_auditoria_gerada set base = 3;');
        });

        $this->assertSame(1, $registros);
        $this->assertSame(6, $this->ultimoRegistroDeAuditoria('teste_auditoria_gerada')->after->dobro);

        $registros = $this->contarRegistrosDeAuditoria('teste_auditoria_gerada', function () {
            DB::update('update public.teste_auditoria_gerada set base = base;');
        });

        $this->assertSame(0, $registros);
    }

    /**
     * A auditoria registra a linha como foi efetivamente gravada, depois das
     * triggers BEFORE da tabela; mudança desfeita por uma BEFORE não conta.
     */
    public function test_linha_alterada_por_trigger_before_e_registrada_com_o_valor_final(): void
    {
        DB::unprepared('create function public.teste_auditoria_before() returns trigger language plpgsql as $$ begin new.nome := upper(new.nome); return new; end; $$;');
        DB::unprepared('create trigger teste_auditoria_before before update on public.teste_auditoria for each row execute procedure public.teste_auditoria_before();');

        DB::insert("insert into public.teste_auditoria (nome) values ('inicial');");

        $registros = $this->contarRegistrosDeAuditoria('teste_auditoria', function () {
            DB::update("update public.teste_auditoria set nome = 'alterado';");
        });

        $this->assertSame(1, $registros);
        $this->assertSame('ALTERADO', $this->ultimoRegistroDeAuditoria('teste_auditoria')->after->nome);

        DB::unprepared('create or replace function public.teste_auditoria_before() returns trigger language plpgsql as $$ begin new.nome := old.nome; return new; end; $$;');

        $registros = $this->contarRegistrosDeAuditoria('teste_auditoria', function () {
            DB::update("update public.teste_auditoria set nome = 'descartado';");
        });

        $this->assertSame(0, $registros);
    }

    /**
     * Limitação aceita, igual à das gerações anteriores: a trigger é por linha
     * e o TRUNCATE não dispara trigger por linha, então esvazia a tabela sem
     * deixar rastro na auditoria.
     */
    public function test_truncate_esvazia_a_tabela_sem_registro(): void
    {
        DB::insert("insert into public.teste_auditoria (nome) values ('um'), ('dois');");

        $registros = $this->contarRegistrosDeAuditoria('teste_auditoria', function () {
            DB::unprepared('truncate public.teste_auditoria;');
        });

        $this->assertSame(0, $registros);
        $this->assertSame(0, DB::scalar('select count(*) from public.teste_auditoria;'));
    }

    /**
     * O RESET deixa o parâmetro de sessão com string vazia, e não com o valor
     * original. A escrita não pode abortar nesse estado e a auditoria deve
     * seguir ligada, que é o comportamento padrão.
     */
    public function test_desligar_e_religar_auditoria_pela_sessao(): void
    {
        DB::insert("insert into public.teste_auditoria (nome) values ('inicial');");

        DB::unprepared('set "audit.enabled" = \'false\';');

        $registros = $this->contarRegistrosDeAuditoria('teste_auditoria', function () {
            DB::update("update public.teste_auditoria set nome = 'sem auditoria';");
        });

        $this->assertSame(0, $registros);

        DB::unprepared('reset "audit.enabled";');

        $this->assertSame('', DB::scalar("select current_setting('audit.enabled', true);"));

        $registros = $this->contarRegistrosDeAuditoria('teste_auditoria', function () {
            DB::update("update public.teste_auditoria set nome = 'com auditoria';");
        });

        $this->assertSame(1, $registros);
    }

    public function test_valor_invalido_no_parametro_nao_interrompe_a_alteracao(): void
    {
        DB::insert("insert into public.teste_auditoria (nome) values ('inicial');");

        DB::unprepared('set "audit.enabled" = \'invalido\';');

        $registros = $this->contarRegistrosDeAuditoria('teste_auditoria', function () {
            DB::update("update public.teste_auditoria set nome = 'alterado';");
        });

        $this->assertSame(1, $registros);
    }

    public function test_contexto_invalido_usa_o_contexto_padrao(): void
    {
        DB::insert("insert into public.teste_auditoria (nome) values ('inicial');");

        DB::unprepared('set "audit.context" = \'{quebrado\';');

        $registros = $this->contarRegistrosDeAuditoria('teste_auditoria', function () {
            DB::update("update public.teste_auditoria set nome = 'alterado';");
        });

        $this->assertSame(1, $registros);
        $this->assertSame(0, $this->ultimoRegistroDeAuditoria('teste_auditoria')->context->user_id);
    }

    public function test_contexto_da_sessao_e_gravado_no_registro(): void
    {
        DB::insert("insert into public.teste_auditoria (nome) values ('inicial');");

        DB::unprepared('set "audit.context" = \'{"user_id": 123, "user_name": "Fulano", "origin": "http://localhost/teste"}\';');

        DB::update("update public.teste_auditoria set nome = 'alterado';");

        $contexto = $this->ultimoRegistroDeAuditoria('teste_auditoria')->context;

        $this->assertSame(123, $contexto->user_id);
        $this->assertSame('Fulano', $contexto->user_name);
        $this->assertSame('http://localhost/teste', $contexto->origin);
    }
}
