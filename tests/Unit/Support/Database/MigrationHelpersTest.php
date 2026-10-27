<?php

use App\Models\LegacyPerson;
use App\Models\LegacyRace;
use App\Models\LegacySchool;
use App\Setting;
use App\SettingCategory;
use App\Support\Database\AsView;
use App\Support\Database\AuditTrigger;
use App\Support\Database\CsvSeeder;
use App\Support\Database\DropForeignKey;
use App\Support\Database\DropPrimaryKey;
use App\Support\Database\EnableDisableForeignKeys;
use App\Support\Database\IncrementSequence;
use App\Support\Database\MigrationUtils;
use App\Support\Database\PrimaryKey;
use App\Support\Database\SettingCategoryTrait;
use App\Support\Database\SpecificReport;
use App\Support\Database\UnknowUser;
use App\Support\Database\UpdatedAtTrigger;
use Database\Factories\LegacyPersonFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

// Os auxiliares das migrações mexem na estrutura; no Postgres isso também
// volta com o rollback da transação do teste.
function auxiliaresDeMigracao(): object
{
    return new class
    {
        use AsView, MigrationUtils {
            AsView::dropView insteadof MigrationUtils;
            MigrationUtils::dropView as dropViewDoUtils;
        }
        use AuditTrigger;
        use DropForeignKey;
        use DropPrimaryKey;
        use EnableDisableForeignKeys;
        use IncrementSequence;
        use PrimaryKey;
        use SettingCategoryTrait;
        use SpecificReport;
        use UnknowUser;
        use UpdatedAtTrigger;

        public function chama(string $metodo, ...$argumentos)
        {
            return $this->$metodo(...$argumentos);
        }
    };
}

function constraintsDa(string $tabela, string $tipo): array
{
    return collect(DB::select("select constraint_name from information_schema.table_constraints where table_schema = 'public' and table_name = ? and constraint_type = ?", [$tabela, $tipo]))
        ->pluck('constraint_name')->all();
}

test('troca a chave primária composta por um id e desfaz a troca', function () {
    DB::unprepared('create table public.cobertura_chave (a int, b int, primary key (a, b))');
    $helpers = auxiliaresDeMigracao();

    $helpers->createPrimaryKey('public.cobertura_chave');

    expect(Schema::hasColumn('public.cobertura_chave', 'id'))->toBeTrue()
        ->and(constraintsDa('cobertura_chave', 'UNIQUE'))->toHaveCount(1)
        ->and($helpers->getTableName('public.cobertura_chave'))->toBe('cobertura_chave');

    $helpers->removePrimaryKey('public.cobertura_chave', ['a', 'b']);

    expect(Schema::hasColumn('public.cobertura_chave', 'id'))->toBeFalse()
        ->and(constraintsDa('cobertura_chave', 'PRIMARY KEY'))->toHaveCount(1);
});

test('cria o id na tabela sem chave e manipula índices, chaves estrangeiras e constraints', function () {
    DB::unprepared('create table public.cobertura_pai (id int primary key); create table public.cobertura_sem_chave (pai_id int, codigo int)');
    $helpers = auxiliaresDeMigracao();

    $helpers->createPrimaryKey('public.cobertura_sem_chave');
    $helpers->createUniqueIndex('public.cobertura_sem_chave', ['codigo']);

    Schema::table('public.cobertura_sem_chave', fn ($table) => $table->foreign('pai_id', 'cobertura_pai_fk')->references('id')->on('public.cobertura_pai'));
    $helpers->dropForeignKey('public.cobertura_sem_chave', 'cobertura_pai_fk');

    expect(constraintsDa('cobertura_sem_chave', 'FOREIGN KEY'))->toBe([])
        ->and(constraintsDa('cobertura_sem_chave', 'UNIQUE'))->toHaveCount(1);

    DB::unprepared('create table public.cobertura_constraint (x int not null)');
    $helpers->createConstraint('public.cobertura_constraint', ['x'], 'cobertura_constraint_pk');

    expect(constraintsDa('cobertura_constraint', 'PRIMARY KEY'))->toBe(['cobertura_constraint_pk']);
});

test('remove as chaves estrangeiras de uma tabela e as que apontam para ela', function () {
    DB::unprepared('
        create table public.cobertura_origem (id int primary key);
        create table public.cobertura_filha (id int primary key, origem_id int references public.cobertura_origem (id));
        create table public.cobertura_neta (filha_id int references public.cobertura_filha (id));
    ');
    $helpers = auxiliaresDeMigracao();

    $helpers->chama('dropForeignKeysIn', 'cobertura_filha');

    expect(constraintsDa('cobertura_filha', 'FOREIGN KEY'))->toBe([])
        ->and(constraintsDa('cobertura_neta', 'FOREIGN KEY'))->toHaveCount(1);

    $helpers->chama('dropForeignKeysFromTable', 'cobertura_filha');

    expect(constraintsDa('cobertura_neta', 'FOREIGN KEY'))->toBe([]);
});

test('cria e remove o gatilho que atualiza o updated_at', function () {
    DB::unprepared('create table public.cobertura_gatilho (id int, updated_at timestamp)');
    $helpers = auxiliaresDeMigracao();
    $gatilho = fn () => DB::table('information_schema.triggers')->where('trigger_name', 'update_public_cobertura_gatilho')->exists();

    $helpers->createUpdatedAtTrigger('public.cobertura_gatilho');

    expect($gatilho())->toBeTrue();

    $helpers->dropUpdatedAtTrigger('public.cobertura_gatilho');

    expect($gatilho())->toBeFalse();
});

test('o seeder de CSV exige o arquivo e o modelo e grava cada linha', function () {
    $pessoa = LegacyPersonFactory::new()->create()->getKey();
    $csv = tempnam(sys_get_temp_dir(), 'csv');
    file_put_contents($csv, "nm_raca,raca_educacenso,idpes_cad\nRaça de Teste,,{$pessoa}\n");

    $seeder = new class extends CsvSeeder {};

    expect(fn () => $seeder->getCsvFilename())->toThrow(Exception::class, 'CSV file not defined.')
        ->and(fn () => $seeder->getModelName())->toThrow(Exception::class, 'Model name not defined.');

    $seeder = new class($csv) extends CsvSeeder
    {
        public function __construct(string $filename)
        {
            $this->filename = $filename;
            $this->model = LegacyRace::class;
        }
    };

    try {
        $seeder->run();
    } finally {
        unlink($csv);
    }

    expect(LegacyRace::query()->where('nm_raca', 'Raça de Teste')->whereNull('raca_educacenso')->exists())->toBeTrue();
});

test('remove a chave primária, ajusta a sequência e liga e desliga os gatilhos da tabela', function () {
    DB::unprepared('create table public.cobertura_sequencia (id serial primary key, nome text)');
    DB::table('public.cobertura_sequencia')->insert([['id' => 41, 'nome' => 'a']]);
    $helpers = auxiliaresDeMigracao();

    $helpers->incrementSequence('public.cobertura_sequencia');
    $helpers->incrementSequence(LegacySchool::class);
    $helpers->chama('disableForeignKeys', 'public.cobertura_sequencia');
    $helpers->chama('enableForeignKeys', 'public.cobertura_sequencia');
    $helpers->chama('dropPrimaryKeysFromTable', 'cobertura_sequencia');

    expect(DB::table('public.cobertura_sequencia')->insertGetId(['nome' => 'b']))->toBe(42)
        ->and(constraintsDa('cobertura_sequencia', 'PRIMARY KEY'))->toBe([]);
});

test('cria e remove views, views materializadas, executa SQL e recria os gatilhos de auditoria', function () {
    $helpers = auxiliaresDeMigracao();
    $sql = tempnam(sys_get_temp_dir(), 'sql');
    file_put_contents($sql, 'create materialized view public.cobertura_materializada as select 1 as um');

    try {
        $helpers->createView('persons');
        $helpers->dropView('public.persons');
        $helpers->createView('persons');
        $helpers->dropViewDoUtils('public.persons');
        $helpers->dropView('public.educacenso_record20');
        $helpers->createView('public.educacenso_record20', '2026-06-17');
        $helpers->executeSqlFile($sql);
        $helpers->refreshMaterializedView('public.cobertura_materializada');
        $helpers->dropMaterializedView('public.cobertura_materializada');
    } finally {
        unlink($sql);
    }

    config(['audit.skip' => []]);
    $helpers->dropAuditTriggers();
    $helpers->createAuditTriggers();

    expect(DB::table('information_schema.views')->where('table_name', 'persons')->exists())->toBeFalse()
        ->and(DB::table('information_schema.triggers')->where('trigger_name', 'like', '%_audit')->exists())->toBeTrue();
});

test('acha o usuário desconhecido, a categoria de configuração pelo nome e os relatórios do cliente', function () {
    $helpers = auxiliaresDeMigracao();
    LegacyPerson::query()->where('nome', 'Desconhecido')->update(['nome' => 'Desconhecido antigo']);

    expect($helpers->checkUnknowUserExists())->toBeFalse()
        ->and(fn () => $helpers->getUnknowUserId())->toThrow(Exception::class, 'Unknow user not found');

    $pessoa = LegacyPersonFactory::new()->create(['nome' => 'Desconhecido']);
    $categoria = SettingCategory::query()->firstOrFail();
    Setting::query()->updateOrCreate(['key' => 'legacy.report.mostrar_relatorios'], ['value' => 'cliente-a', 'setting_category_id' => $categoria->id, 'type' => 'string']);

    expect($helpers->getUnknowUserId())->toBe($pessoa->getKey())
        ->and($helpers->chama('getSettingCategoryIdByName', $categoria->name))->toBe($categoria->id)
        ->and($helpers->chama('getSettingCategoryIdByName', 'categoria que não existe'))->toBe(SettingCategory::query()->first()->id)
        ->and($helpers->isReportFor('cliente-a'))->toBeTrue()
        ->and($helpers->isNotReportFor('cliente-b'))->toBeTrue();
});
