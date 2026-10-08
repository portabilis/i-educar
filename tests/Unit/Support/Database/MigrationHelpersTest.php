<?php

use App\Models\LegacyRace;
use App\Support\Database\CsvSeeder;
use App\Support\Database\DropForeignKey;
use App\Support\Database\PrimaryKey;
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
        use DropForeignKey;
        use PrimaryKey;
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
