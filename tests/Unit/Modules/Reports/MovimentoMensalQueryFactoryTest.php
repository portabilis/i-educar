<?php

use iEducar\Modules\Reports\QueryFactory\MovimentoMensalMatReclassificadoseQueryFactory;
use iEducar\Modules\Reports\QueryFactory\MovimentoMensalMatReclassificadosQueryFactory;
use iEducar\Modules\Reports\QueryFactory\MovimentoMensalMatTrocaeQueryFactory;
use iEducar\Modules\Reports\QueryFactory\MovimentoMensalMatTrocasQueryFactory;
use iEducar\Modules\Reports\QueryFactory\MovimentoMensalQueryFactory;

test('o movimento mensal repete os totais de início e fim nas colunas do relatório', function () {
    $statement = Mockery::mock(PDOStatement::class);
    $statement->shouldReceive('execute')->andReturnTrue();
    $statement->shouldReceive('fetchAll')->andReturn([['mat_ini' => 10, 'mat_fim_m' => 4, 'mat_fim_f' => 5, 'mat_fim' => 9]]);
    $pdo = Mockery::mock(PDO::class);
    $pdo->shouldReceive('prepare')->andReturn($statement);

    $data = (new MovimentoMensalQueryFactory($pdo, ['data_inicial' => '2030-01-01', 'data_final' => '2030-01-31', 'escola' => 1, 'ano' => 2030]))->getData();

    expect($data[0])->toMatchArray(['mat_ini_t' => 10, 'mat_final_m' => 4, 'mat_final_f' => 5, 'mat_final_t' => 9]);
});

test('os detalhes de reclassificados e trocas de turma filtram cada movimento', function (string $factory, string $where) {
    expect((new $factory(Mockery::mock(PDO::class)))->where())->toBe($where);
})->with([
    'reclassificados' => [MovimentoMensalMatReclassificadosQueryFactory::class, 'reclassificado and saiu_durante'],
    'entradas por reclassificação' => [MovimentoMensalMatReclassificadoseQueryFactory::class, 'matricula_ativa and entrada_reclassificado and entrou_durante'],
    'entradas por troca' => [MovimentoMensalMatTrocaeQueryFactory::class, 'matricula_ativa and entrou_durante and sequencial > 1'],
    'saídas por troca' => [MovimentoMensalMatTrocasQueryFactory::class, 'matricula_ativa and enturmacao_remanejado and enturmacao_inativa and saiu_durante and sequencial < maior_sequencial'],
]);
