<?php

namespace Tests\Feature\Audit;

use App\Support\Database\AuditTrigger;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/**
 * Base dos testes de auditoria. Instala o que falta dentro da transação de
 * cada teste (DDL é transacional no PostgreSQL), então nada persiste no banco
 * após o rollback e os testes funcionam tanto em banco sem auditoria (CI)
 * quanto em banco com auditoria já instalada.
 */
abstract class AuditTestCase extends TestCase
{
    use AuditTrigger;
    use DatabaseTransactions;

    protected const TABELA = 'public.teste_auditoria';

    protected function setUp(): void
    {
        parent::setUp();

        if (!Schema::hasTable('ieducar_audit')) {
            Artisan::call('migrate', [
                '--path' => 'database/migrations/audit/2019_12_01_100000_create_audit_table.php',
                '--force' => true,
            ]);
        }

        $this->instalarFuncoesDeAuditoria();
    }

    protected function instalarFuncoesDeAuditoria(): void
    {
        (require base_path('database/migrations/audit/2026_08_14_000001_optimize_audit_functions.php'))->up();
    }

    protected function criarTabelaDeTeste(): void
    {
        DB::unprepared('
            create table public.teste_auditoria (
                id serial primary key,
                nome varchar(255),
                dados json,
                valor double precision,
                updated_at timestamp
            );
        ');
    }

    protected function contarRegistrosDeAuditoria(string $tabela, callable $alteracao): int
    {
        $anterior = DB::scalar('select coalesce(max(id), 0) from public.ieducar_audit;');

        $alteracao();

        return DB::scalar(
            'select count(*) from public.ieducar_audit where id > ? and "table" = ?;',
            [$anterior, $tabela]
        );
    }

    protected function ultimoRegistroDeAuditoria(string $tabela): ?object
    {
        $registro = DB::selectOne(
            'select "schema", "table", context, before, after from public.ieducar_audit where "table" = ? order by id desc limit 1;',
            [$tabela]
        );

        if ($registro) {
            $registro->context = json_decode((string) $registro->context);
            $registro->before = json_decode((string) $registro->before);
            $registro->after = json_decode((string) $registro->after);
        }

        return $registro;
    }

    protected function contarTriggersDaTabela(string $tabela, ?string $conexao = null): int
    {
        return DB::connection($conexao)->scalar(
            "select count(*) from pg_trigger where tgrelid = ?::regclass and tgfoid = 'public.audit()'::regprocedure and not tgisinternal;",
            [$tabela]
        );
    }
}
