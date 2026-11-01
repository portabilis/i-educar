<?php

use Database\Factories\LegacyCourseFactory;
use Database\Factories\LegacySchoolFactory;
use Database\Factories\LegacyUserFactory;
use Database\Factories\LegacyUserSchoolFactory;
use Database\Factories\LegacyUserTypeFactory;

function consultaMovimentoGeral(array $params = []): string
{
    return test()->get('/intranet/educar_consulta_movimento_geral_lst.php?' . http_build_query([
        'ano' => now()->year,
        'data_inicial' => now()->startOfYear()->format('d/m/Y'),
        'data_final' => now()->format('d/m/Y'),
        ...$params,
    ]))->assertOk()->getContent();
}

test('sem curso mostra todos nos parâmetros da consulta', function () {
    $this->actingAs(LegacyUserFactory::new()->admin()->create());

    expect(consultaMovimentoGeral())
        ->toContain("<td class='formlttd' style='vertical-align: top; text-align:left'>Todos</td>");
});

test('com curso mostra os nomes dos cursos nos parâmetros da consulta', function () {
    $this->actingAs(LegacyUserFactory::new()->admin()->create());
    $cursoA = LegacyCourseFactory::new()->create(['nm_curso' => 'Curso Alfa']);
    $cursoB = LegacyCourseFactory::new()->create(['nm_curso' => 'Curso Beta']);

    expect(consultaMovimentoGeral(['curso' => [$cursoA->getKey(), $cursoB->getKey()]]))
        ->toContain('Curso Alfa<br>Curso Beta');
});

// A consulta dos totais abre uma conexão PDO própria, que não enxerga os
// dados da transação do teste; os links são conferidos chamando o método direto
// na tela, que o arquivo devolve pronta para uso.
test('troca os totais pelos links da consulta detalhada só nas escolas que o usuário acessa', function () {
    $escola = LegacySchoolFactory::new()->create();
    $outraEscola = LegacySchoolFactory::new()->create();
    $user = LegacyUserFactory::new()->create([
        'ref_cod_tipo_usuario' => LegacyUserTypeFactory::new()->create(['nivel' => App_Model_NivelTipoUsuario::ESCOLA]),
    ]);
    LegacyUserSchoolFactory::new()->create(['ref_cod_usuario' => $user, 'ref_cod_escola' => $escola]);
    $this->actingAs($user);

    $linha = fn ($cod) => [
        'cod_escola' => $cod,
        'escola' => 'Escola',
        'ciclo' => '*',
        'aee' => '',
        'ed_inf_int' => 3,
        'localizacao' => 'Urbana',
    ];
    $params = ['ano' => '2026', 'curso' => '', 'seleciona_curso' => 0];

    $page = require base_path('ieducar/intranet/educar_consulta_movimento_geral_lst.php'); // NOSONAR
    $dados = (fn () => $this->adicionarLinksDasEscolas(
        data: [$linha($escola->getKey()), $linha($outraEscola->getKey())],
        params: $params,
    ))->call($page);

    expect($dados[0])->toBe([
        'cod_escola' => $escola->getKey(),
        'escola' => 'Escola',
        'ciclo' => '*',
        'aee' => '',
        'ed_inf_int' => sprintf(
            '<a href="#" class="mostra-consulta" style="font-weight: bold;" data-api="ConsultaMovimentoGeral" data-params=\'%s\' data-tipo="ed_inf_int">3</a>',
            json_encode([...$params, 'escola' => $escola->getKey()])
        ),
        'localizacao' => 'Urbana',
    ])->and($dados[1])->toBe($linha($outraEscola->getKey()));
});
