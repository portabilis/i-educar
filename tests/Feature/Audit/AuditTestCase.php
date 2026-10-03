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

        $this->installAuditFunctions();
    }

    protected function installAuditFunctions(): void
    {
        (require base_path('database/migrations/audit/2026_08_14_000001_optimize_audit_functions.php'))->up();
    }

    protected function createTestTable(): void
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

    protected function countAuditRecords(string $table, callable $write): int
    {
        $previous = DB::scalar('select coalesce(max(id), 0) from public.ieducar_audit;');

        $write();

        return DB::scalar(
            'select count(*) from public.ieducar_audit where id > ? and "table" = ?;',
            [$previous, $table]
        );
    }

    protected function lastAuditRecord(string $table): ?object
    {
        $record = DB::selectOne(
            'select "schema", "table", context, before, after from public.ieducar_audit where "table" = ? order by id desc limit 1;',
            [$table]
        );

        if ($record) {
            $record->context = json_decode((string) $record->context);
            $record->before = json_decode((string) $record->before);
            $record->after = json_decode((string) $record->after);
        }

        return $record;
    }

    protected function countTableTriggers(string $table, ?string $connection = null): int
    {
        return DB::connection($connection)->scalar(
            "select count(*) from pg_trigger where tgrelid = ?::regclass and tgfoid = 'public.audit()'::regprocedure and not tgisinternal;",
            [$table]
        );
    }
}
