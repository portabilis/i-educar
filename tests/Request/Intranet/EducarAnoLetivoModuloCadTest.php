<?php

use App\Models\LegacyAcademicYearStage;
use App\Models\LegacySchoolAcademicYear;
use Database\Factories\LegacyAcademicYearStageFactory;
use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacySchoolAcademicYearFactory;
use Database\Factories\LegacySchoolFactory;
use Database\Factories\LegacyStageTypeFactory;
use Database\Factories\LegacyUserFactory;
use Tests\Request\Support\LegacyFields;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);

    $this->institution = ['ref_cod_instituicao' => $this->user->ref_cod_instituicao];
    $this->school = LegacySchoolFactory::new()->create($this->institution);
    $this->stageType = LegacyStageTypeFactory::new()->create($this->institution + ['nm_tipo' => 'Semestre', 'num_etapas' => 2]);

    $this->url = fn (int $year, string $extra = '') => "/intranet/educar_ano_letivo_modulo_cad.php?ref_cod_escola={$this->school->getKey()}&ano={$year}{$extra}";

    // Um ano letivo com duas etapas semestrais.
    $this->year = function (int $year, array $attributes = []) {
        $academicYear = LegacySchoolAcademicYearFactory::new()->create($attributes + [
            'ref_cod_escola' => $this->school,
            'ano' => $year,
            'andamento' => 0,
        ]);

        foreach ([1 => ["{$year}-02-01", "{$year}-06-30"], 2 => ["{$year}-08-01", "{$year}-12-15"]] as $sequence => [$start, $end]) {
            LegacyAcademicYearStageFactory::new()->create([
                'escola_ano_letivo_id' => $academicYear,
                'ref_ano' => $year,
                'ref_ref_cod_escola' => $this->school->getKey(),
                'sequencial' => $sequence,
                'ref_cod_modulo' => $this->stageType,
                'data_inicio' => $start,
                'data_fim' => $end,
                'dias_letivos' => 100,
            ]);
        }

        return $academicYear;
    };

    // As etapas vão em três listas paralelas, uma linha por etapa.
    $this->stages = fn (int $year) => [
        'ref_cod_modulo' => $this->stageType->getKey(),
        'data_inicio' => ["01/02/{$year}", "01/08/{$year}"],
        'data_fim' => ["30/06/{$year}", "15/12/{$year}"],
        'dias_letivos' => [100, 100],
    ];

    $this->saved = fn (int $year) => LegacyAcademicYearStage::query()
        ->where('ref_ref_cod_escola', $this->school->getKey())
        ->where('ref_ano', $year)
        ->orderBy('sequencial')
        ->get()
        ->map(fn ($stage) => [$stage->sequencial, $stage->data_inicio->format('Y-m-d'), $stage->data_fim->format('Y-m-d'), $stage->dias_letivos])
        ->all();
});

test('abre o cadastro das etapas de um ano que a escola ainda não tem', function () {
    $response = $this->get(($this->url)(2030, '&referrer=educar_escola_det.php'))->assertOk();
    $fields = LegacyFields::from($response->getContent());

    expect($fields['tipoacao'])->toBe('Novo')
        ->and($fields['ref_ano'])->toBe('2030')
        ->and($fields['ref_ref_cod_escola'])->toBe((string) $this->school->getKey())
        // Para o ano novo, copiar as turmas do ano anterior vem marcado.
        ->and($fields['copiar_turmas'])->toBe('on');

    // O cancelar volta para a tela que chamou.
    $response->assertSee('educar_escola_det.php?cod_escola=' . $this->school->getKey(), false);
});

test('mostra as etapas do ano anterior como exemplo', function () {
    ($this->year)(2029);

    $this->get(($this->url)(2030))
        ->assertOk()
        ->assertSee('Etapas do ano anterior (2029)')
        ->assertSee('01/02/2029 à 30/06/2029')
        ->assertSee('Adicione as etapas abaixo para 2030 semelhante ao exemplo do ano anterior')
        ->assertDontSee('não é um ano bissexto');
});

test('avisa quando uma etapa do ano anterior cai em 29 de fevereiro', function () {
    $previous = LegacySchoolAcademicYearFactory::new()->create(['ref_cod_escola' => $this->school, 'ano' => 2028, 'andamento' => 0]);

    LegacyAcademicYearStageFactory::new()->create([
        'escola_ano_letivo_id' => $previous,
        'ref_ano' => 2028,
        'ref_ref_cod_escola' => $this->school->getKey(),
        'ref_cod_modulo' => $this->stageType,
        'data_inicio' => '2028-02-29',
        'data_fim' => '2028-06-30',
    ]);

    $this->get(($this->url)(2029))
        ->assertOk()
        ->assertSee('A data 29/02/2029 não poderá ser migrada pois 2029 não é um ano bissexto');
});

test('abre a edição com as etapas gravadas', function () {
    ($this->year)(2030);

    $fields = LegacyFields::from($this->get(($this->url)(2030))->assertOk()->getContent());

    expect($fields['tipoacao'])->toBe('Editar')
        ->and($fields['ref_cod_modulo'])->toBe((string) $this->stageType->getKey())
        ->and($fields['data_inicio'])->toBe(['01/02/2030', '01/08/2030'])
        ->and($fields['data_fim'])->toBe(['30/06/2030', '15/12/2030'])
        ->and($fields['dias_letivos'])->toBe(['100', '100']);
});

test('cadastra o ano letivo da escola com as etapas', function () {
    $payload = LegacyFields::from($this->get(($this->url)(2030))->assertOk()->getContent(), ($this->stages)(2030) + [
        'copiar_alocacoes_e_vinculos_professores' => 'on',
        'copiar_alocacoes_demais_servidores' => 'on',
    ]);

    $this->post(($this->url)(2030), $payload)
        ->assertRedirect('educar_escola_det.php?cod_escola=' . $this->school->getKey() . '#ano_letivo');

    $year = LegacySchoolAcademicYear::query()->where('ref_cod_escola', $this->school->getKey())->where('ano', 2030)->sole();

    expect($year->ativo)->toBe(1)
        ->and((bool) $year->copia_turmas)->toBeTrue()
        ->and((bool) $year->copia_dados_professor)->toBeTrue()
        ->and((bool) $year->copia_dados_demais_servidores)->toBeTrue()
        ->and(($this->saved)(2030))->toBe([
            [1, '2030-02-01', '2030-06-30', 100],
            [2, '2030-08-01', '2030-12-15', 100],
        ]);
});

test('recusa o cadastro e devolve o formulário', function (Closure $overrides, array $without, string $message) {
    ($this->year)(2029);

    $payload = LegacyFields::from($this->get(($this->url)(2030))->assertOk()->getContent(), $overrides->call($this) + ($this->stages)(2030));

    $this->post(($this->url)(2030), array_diff_key($payload, array_flip($without)))
        ->assertOk()
        ->assertSee($message);

    expect(LegacySchoolAcademicYear::query()->where('ref_cod_escola', $this->school->getKey())->where('ano', 2030)->exists())->toBeFalse();
})->with([
    'sem escolher o tipo de etapa' => [fn () => ['ref_cod_modulo' => ''], [], 'Cadastro não realizado.'],
    'sem o tipo de etapa e sem copiar as turmas' => [fn () => ['ref_cod_modulo' => ''], ['copiar_turmas'], 'Cadastro não realizado.'],
    'etapa dentro do período de outro ano letivo' => [
        fn () => ['data_inicio' => ['01/12/2029', '01/08/2030']],
        [],
        'A data informada não pode fazer parte do período configurado para outros anos letivos.',
    ],
]);

test('regrava as etapas de um ano letivo existente', function () {
    ($this->year)(2030);

    $payload = LegacyFields::from($this->get(($this->url)(2030))->assertOk()->getContent(), [
        'data_inicio' => ['10/02/2030', '05/08/2030'],
        'data_fim' => ['25/06/2030', '10/12/2030'],
        'dias_letivos' => [95, 105],
    ]);

    $this->post(($this->url)(2030), $payload)->assertRedirect('educar_escola_lst.php');

    expect(($this->saved)(2030))->toBe([
        [1, '2030-02-10', '2030-06-25', 95],
        [2, '2030-08-05', '2030-12-10', 105],
    ]);
});

test('recusa a edição e mantém as etapas gravadas', function (Closure $overrides, array $without, string $message) {
    ($this->year)(2029);
    ($this->year)(2030);

    $payload = LegacyFields::from($this->get(($this->url)(2030))->assertOk()->getContent(), $overrides->call($this));

    $this->post(($this->url)(2030), array_diff_key($payload, array_flip($without)))
        ->assertOk()
        ->assertSee($message, false);

    expect(($this->saved)(2030))->toBe([
        [1, '2030-02-01', '2030-06-30', 100],
        [2, '2030-08-01', '2030-12-15', 100],
    ]);
})->with([
    'sem o tipo de etapa' => [fn () => ['ref_cod_modulo' => ''], [], 'É necessário adicionar pelo menos uma etapa!'],
    'etapa dentro do período de outro ano letivo' => [
        fn () => ['data_inicio' => ['01/12/2029', '01/08/2030']],
        [],
        'A data informada não pode fazer parte do período configurado para outros anos letivos.',
    ],
]);

// Defeito conhecido: a tela repassa as listas de datas ao serviço sem conferir
// se vieram. Enviado sem nenhuma linha de etapa, o formulário estoura por tipo
// em vez de mostrar o aviso de que falta etapa.
test('defeito: o formulário sem nenhuma linha de etapa estoura', function (int $year) {
    ($this->year)(2030);

    $payload = LegacyFields::from($this->get(($this->url)($year))->assertOk()->getContent(), ($this->stages)($year));

    $this->post(($this->url)($year), array_diff_key($payload, array_flip(['data_inicio', 'data_fim', 'dias_letivos'])))
        ->assertServerError();
})->with([
    'no cadastro' => 2031,
    'na edição' => 2030,
]);

// Defeito conhecido: o serviço grava o ano letivo e só depois valida cada
// etapa, sem transação. Com uma etapa inválida a tela avisa o erro, mas o ano
// letivo já ficou criado, sem etapa nenhuma.
test('defeito: o cadastro recusado por etapa inválida deixa o ano letivo criado sem etapas', function () {
    $payload = LegacyFields::from($this->get(($this->url)(2030))->assertOk()->getContent(), ['dias_letivos' => ['cem', 100]] + ($this->stages)(2030));

    $this->post(($this->url)(2030), $payload)
        ->assertOk()
        ->assertSee('Dados do módulo inválidos para a escola');

    expect(LegacySchoolAcademicYear::query()->where('ref_cod_escola', $this->school->getKey())->where('ano', 2030)->exists())->toBeTrue()
        ->and(($this->saved)(2030))->toBe([]);
});

// Defeito conhecido: na edição o serviço apaga as etapas antes de validar as
// novas, sem transação. Com uma etapa inválida a tela avisa o erro e o ano
// letivo fica sem as etapas que tinha.
test('defeito: a edição recusada por etapa inválida apaga as etapas que existiam', function () {
    ($this->year)(2030);

    $payload = LegacyFields::from($this->get(($this->url)(2030))->assertOk()->getContent(), ['dias_letivos' => ['cem', 100]]);

    $this->post(($this->url)(2030), $payload)
        ->assertOk()
        ->assertSee('Dados do módulo inválidos para a escola');

    expect(($this->saved)(2030))->toBe([]);
});

test('excluir o ano letivo desativa o ano, apaga as etapas e volta para a listagem de escolas', function () {
    $year = ($this->year)(2030);

    $payload = LegacyFields::from($this->get(($this->url)(2030))->assertOk()->getContent(), ['tipoacao' => 'Excluir']);

    $this->post(($this->url)(2030), $payload)
        ->assertRedirect('educar_escola_lst.php');

    expect($year->refresh()->ativo)->toBe(0)
        ->and(($this->saved)(2030))->toBe([]);
});

test('volta para a listagem de escolas quando o usuário só pode visualizar escola', function () {
    LegacyGeneralConfigurationFactory::new()->create($this->institution + ['active_on_ieducar' => true]);

    $this->actingAs(LegacyUserFactory::new()->institutional()->withAccess(561, modify: false, remove: false)->create());

    $this->get(($this->url)(2030))->assertRedirect('educar_escola_lst.php');
});
