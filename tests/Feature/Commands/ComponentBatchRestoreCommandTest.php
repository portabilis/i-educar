<?php

use App\Models\ComponentBatchOperation;
use App\Models\Enums\ComponentBatchStatus;
use App\Services\ComponentBatchManagerService;
use Database\Factories\LegacyUserFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

beforeEach(function () {
    $this->operation = fn (ComponentBatchStatus $status = ComponentBatchStatus::COMPLETED, ?array $backup = null) => ComponentBatchOperation::query()->create([
        'user_id' => LegacyUserFactory::new()->create()->getKey(),
        'status_id' => $status->value,
        'data' => ['year' => 2030, 'school_ids' => [1, 2], 'grade_ids' => [3], 'discipline_ids' => [4, 5, 6]],
        'backup' => $backup,
    ]);

    $this->backup = [
        'deleted' => [
            'modules.nota_componente_curricular' => ['pk' => ['nota_aluno_id', 'componente_curricular_id', 'etapa'], 'rows' => [[], []]],
            'tabela.desconhecida' => ['rows' => [[]]],
        ],
        'updated' => [
            'pmieducar.escola_serie_disciplina' => ['pk' => ['id'], 'rows' => [[]]],
        ],
        'touched' => ['modules.professor_turma' => ['turma_ids' => [7]]],
    ];
});

test('avisa quando a operação não existe', function () {
    $this->artisan('batch:restore', ['id' => 999999999])
        ->expectsOutput('Operação não encontrada.')
        ->assertExitCode(1);
});

test('só restaura operações concluídas', function () {
    $operation = ($this->operation)(ComponentBatchStatus::FAILED, $this->backup);

    $this->artisan('batch:restore', ['id' => $operation->getKey()])
        ->expectsOutput("Operação com status 'Falhou'. Apenas operações concluídas podem ser restauradas.")
        ->assertExitCode(1);
});

test('avisa quando não há backup', function () {
    $operation = ($this->operation)();

    $this->artisan('batch:restore', ['id' => $operation->getKey()])
        ->expectsOutput('Operação não possui backup para restaurar.')
        ->assertExitCode(1);
});

test('mostra o resumo do backup e cancela se o usuário não confirmar', function () {
    $operation = ($this->operation)(backup: $this->backup);

    $this->artisan('batch:restore', ['id' => $operation->getKey()])
        ->expectsOutputToContain("Operação #{$operation->getKey()} — criada em {$operation->created_at->format('d/m/Y H:i')}")
        ->expectsOutputToContain('  Ano: 2030')
        ->expectsOutputToContain('  Escolas: 2 | Séries: 1 | Componentes: 3')
        ->expectsOutputToContain('Conteúdo do backup:')
        ->expectsOutputToContain('  - Notas: 2 registros deletados')
        ->expectsOutputToContain('  - tabela.desconhecida: 1 registros deletados')
        ->expectsOutputToContain('  - Componentes da série da escola: 1 registros atualizados')
        ->expectsOutputToContain('  - Vínculos professor/turma: tocados (não restaurável)')
        ->expectsOutputToContain('Modo padrão: registros existentes serão mantidos, anos letivos serão mesclados.')
        ->expectsConfirmation('Confirma a restauração?', 'no')
        ->expectsOutput('Operação cancelada.')
        ->assertExitCode(0);

    expect($operation->refresh()->status())->toBe(ComponentBatchStatus::COMPLETED);
});

test('restaura e lista o que foi feito por tabela', function () {
    $operation = ($this->operation)(backup: $this->backup);

    $this->mock(ComponentBatchManagerService::class)
        ->shouldReceive('restore')->once()
        ->withArgs(fn (ComponentBatchOperation $given, bool $force) => $given->is($operation) && $force)
        ->andReturn([
            'modules.nota_componente_curricular' => ['action' => 'reinseridos', 'count' => 2],
            'tabela.desconhecida' => ['action' => 'reinseridos', 'count' => 1],
        ]);

    $this->artisan('batch:restore', ['id' => $operation->getKey(), '--force' => true])
        ->expectsOutput('Modo --force: dados existentes serão sobrescritos pelos valores do backup.')
        ->expectsConfirmation('Confirma a restauração?', 'yes')
        ->expectsOutput('Restauração concluída com sucesso!')
        ->expectsTable(['Tabela', 'Registros', 'Ação'], [
            ['Notas', 2, 'reinseridos'],
            ['tabela.desconhecida', 1, 'reinseridos'],
        ])
        ->assertExitCode(0);
});

test('mostra o erro quando a restauração falha', function () {
    $operation = ($this->operation)(backup: $this->backup);

    $this->mock(ComponentBatchManagerService::class)
        ->shouldReceive('restore')->andThrow(new RuntimeException('Backup corrompido.'));

    $this->artisan('batch:restore', ['id' => $operation->getKey()])
        ->expectsConfirmation('Confirma a restauração?', 'yes')
        ->expectsOutput('Erro na restauração: Backup corrompido.')
        ->assertExitCode(1);
});
