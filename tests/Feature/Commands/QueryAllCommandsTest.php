<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

beforeEach(function () {
    // Só as conexões de clientes entram; aqui elas apontam para o banco dos testes.
    $pgsql = config('database.connections.pgsql');
    config([
        'database.connections' => [
            'pgsql' => $pgsql,
            'cliente_a' => $pgsql,
            'cliente_b' => $pgsql,
            'cliente_quebrado' => ['database' => 'banco_que_nao_existe'] + $pgsql,
        ],
    ]);

    $this->sql = tempnam(sys_get_temp_dir(), 'sql');
    file_put_contents($this->sql, 'select 1 as um');
});

afterEach(function () {
    @unlink($this->sql);
});

test('roda a consulta em cada conexão de cliente e mostra uma tabela, pulando as excluídas e as que falham', function () {
    $this->artisan('query:all', ['--file' => $this->sql, '--no-database' => ['cliente_b']])
        ->expectsTable(['connection', 'um'], [['cliente_a', 1]])
        ->assertSuccessful();

    $this->artisan('query:all', ['--file' => $this->sql, '--database' => ['cliente_b']])
        ->expectsTable(['connection', 'um'], [['cliente_b', 1]])
        ->assertSuccessful();
});

test('roda a consulta em cada conexão de cliente e grava um CSV', function () {
    $csv = tempnam(sys_get_temp_dir(), 'csv');

    try {
        $this->artisan('query:all-csv', ['--file' => $this->sql, '--output' => $csv, '--no-database' => ['cliente_b']])->assertSuccessful();

        expect(file($csv, FILE_IGNORE_NEW_LINES))->toBe(['conexao,um', 'cliente_a,1']);
    } finally {
        unlink($csv);
    }
});
