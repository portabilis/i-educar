<?php

use App\Models\UniformDistribution;
use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacyUserFactory;
use Database\Factories\UniformDistributionFactory;
use Tests\Request\Support\EnrollmentScenario;
use Tests\Request\Support\LegacyFields;
use Tests\Request\Support\LegacyForm;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);

    $this->scenario = EnrollmentScenario::for($this->user, 2030);
    $this->student = $this->scenario->student('Ana Alves');

    $this->distribution = fn (array $attributes = []) => UniformDistributionFactory::new()->create($attributes + [
        'student_id' => $this->student,
        'school_id' => $this->scenario->school,
        'year' => 2030,
        'type' => 'Entregue',
        'distribution_date' => '15/03/2030',
        'complete_kit' => true,
        'socks_qty' => 2,
        'socks_tm' => '34',
    ]);

    $this->new = '/intranet/educar_distribuicao_uniforme_cad.php?ref_cod_aluno=' . $this->student->getKey();
    $this->edit = fn ($distribution) => $this->new . '&cod_distribuicao_uniforme=' . $distribution->getKey();
    $this->list = 'educar_distribuicao_uniforme_lst.php?ref_cod_aluno=' . $this->student->getKey();

    $this->payload = fn (array $overrides = [], ?string $url = null) => LegacyFields::from($this->get($url ?? $this->new)->assertOk()->getContent(), $overrides + [
        'ref_cod_instituicao' => $this->scenario->institution,
        'ref_cod_escola' => $this->scenario->school->getKey(),
        'year' => '2030',
        'type' => 'Solicitado',
    ]);

    $this->stored = fn () => UniformDistribution::query()->where('student_id', $this->student->getKey())->sole();
});

test('abre o cadastro vazio com os tipos possíveis', function () {
    $html = $this->get($this->new)->assertOk()->getContent();
    $fields = LegacyFields::from($html);

    expect($fields['tipoacao'])->toBe('Novo')
        ->and($fields['year'])->toBe('')
        ->and(LegacyForm::from($html)->options('type'))->toBe(['' => 'Tipo', 'Solicitado' => 'Solicitado', 'Entregue' => 'Entregue']);
});

test('cadastra uma solicitação de uniforme com as peças informadas', function () {
    $this->post($this->new, ($this->payload)([
        'coat_pants_qty' => '1',
        'coat_pants_tm' => 'M',
        'socks_qty' => '3',
        'socks_tm' => '36',
    ]))->assertRedirect($this->list);

    expect(($this->stored)())
        ->year->toBe(2030)
        ->type->toBe('Solicitado')
        ->school_id->toBe($this->scenario->school->getKey())
        ->distribution_date->toBeNull()
        ->complete_kit->toBeFalsy()
        ->coat_pants_qty->toBe(1)
        ->coat_pants_tm->toBe('M')
        ->socks_qty->toBe(3)
        ->socks_tm->toBe('36')
        ->sneakers_qty->toBeNull();
});

test('cadastra uma entrega de kit completo com a data', function () {
    $this->post($this->new, ($this->payload)([
        'type' => 'Entregue',
        'distribution_date' => '15/03/2030',
        'complete_kit' => 'on',
    ]))->assertRedirect($this->list);

    $distribution = ($this->stored)();

    expect($distribution->type)->toBe('Entregue')
        ->and($distribution->complete_kit)->toBeTrue()
        ->and($distribution->distribution_date->format('Y-m-d'))->toBe('2030-03-15');
});

test('recusa a entrega sem a data da distribuição', function () {
    $this->post($this->new, ($this->payload)(['type' => 'Entregue']))
        ->assertOk()
        ->assertSee('Informe a Data da distribuição do uniforme.');

    expect(UniformDistribution::query()->where('student_id', $this->student->getKey())->exists())->toBeFalse();
});

test('recusa a segunda distribuição do aluno no mesmo ano', function () {
    ($this->distribution)();

    $this->post($this->new, ($this->payload)())
        ->assertOk()
        ->assertSee('Já existe uma distribuição cadastrada para este ano, por favor, verifique.');

    expect(UniformDistribution::query()->where('student_id', $this->student->getKey())->count())->toBe(1);
});

test('abre a edição com os dados gravados e edita a distribuição', function () {
    $distribution = ($this->distribution)();

    $fields = LegacyFields::from($this->get(($this->edit)($distribution))->assertOk()->assertSee("id='btn_excluir'", false)->getContent());

    expect($fields['tipoacao'])->toBe('Editar')
        ->and($fields['id'])->toBe((string) $distribution->getKey())
        ->and($fields['year'])->toBe('2030')
        ->and($fields['type'])->toBe('Entregue')
        ->and($fields['distribution_date'])->toBe('15/03/2030')
        ->and($fields['socks_qty'])->toBe('2')
        ->and($fields['socks_tm'])->toBe('34')
        ->and($fields)->toHaveKey('complete_kit');

    unset($fields['complete_kit']);

    $this->post(($this->edit)($distribution), [
        'ref_cod_instituicao' => $this->scenario->institution,
        'ref_cod_escola' => $this->scenario->school->getKey(),
        'socks_qty' => '7',
        'distribution_date' => '20/03/2030',
    ] + $fields)->assertRedirect($this->list);

    expect($distribution->refresh())
        ->socks_qty->toBe(7)
        ->complete_kit->toBeFalse()
        ->and($distribution->distribution_date->format('Y-m-d'))->toBe('2030-03-20');
});

test('recusa mudar a distribuição para o ano de outra', function () {
    $distribution = ($this->distribution)();
    ($this->distribution)(['year' => 2031]);

    $payload = ($this->payload)(['year' => '2031'], ($this->edit)($distribution));

    $this->post(($this->edit)($distribution), $payload)
        ->assertOk()
        ->assertSee('Já existe uma distribuição cadastrada para este ano, por favor, verifique.');

    expect($distribution->refresh()->year)->toBe(2030);
});

test('defeito: a edição não consegue trocar o ano da distribuição', function () {
    // A tela procura a distribuição do aluno no ano enviado; num ano ainda sem
    // distribuição não acha nenhuma e responde que "já existe".
    $distribution = ($this->distribution)();

    $payload = ($this->payload)(['year' => '2031'], ($this->edit)($distribution));

    $this->post(($this->edit)($distribution), $payload)
        ->assertOk()
        ->assertSee('Já existe uma distribuição cadastrada para este ano, por favor, verifique.');

    expect($distribution->refresh()->year)->toBe(2030);
});

test('exclui a distribuição', function () {
    $distribution = ($this->distribution)();

    $this->post(($this->edit)($distribution), ($this->payload)(['tipoacao' => 'Excluir'], ($this->edit)($distribution)))
        ->assertRedirect($this->list);

    $this->assertSoftDeleted($distribution);
});

test('com um código inexistente abre o formulário como cadastro', function () {
    $fields = LegacyFields::from($this->get($this->new . '&cod_distribuicao_uniforme=999999999')->assertOk()->getContent());

    expect($fields['tipoacao'])->toBe('Novo');
});

test('defeito: o Cancelar do cadastro não leva o aluno na URL', function () {
    // A tela lê `studend_id`, que não existe.
    $this->get($this->new)->assertOk()->assertSee('goOrClose( "educar_distribuicao_uniforme_lst.php?ref_cod_aluno="', false);
});

test('quem só pode visualizar não abre, não grava e não exclui', function (string $action) {
    LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $this->scenario->institution, 'active_on_ieducar' => true]);

    $distribution = ($this->distribution)();
    $payload = ($this->payload)(['tipoacao' => $action, 'socks_qty' => '7'], ($this->edit)($distribution));

    $this->actingAs(
        LegacyUserFactory::new()->institutional()->withAccess(578, modify: false, remove: false)->create(['ref_cod_instituicao' => $this->scenario->institution])
    );

    $this->get(($this->edit)($distribution))->assertRedirect($this->list);
    $this->post(($this->edit)($distribution), $payload)->assertRedirect($this->list);

    expect($distribution->refresh())
        ->socks_qty->toBe(2)
        ->deleted_at->toBeNull();
})->with(['Novo', 'Editar', 'Excluir']);

test('não oferece excluir a quem não pode remover', function () {
    LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $this->scenario->institution, 'active_on_ieducar' => true]);

    $distribution = ($this->distribution)();

    $this->actingAs(
        LegacyUserFactory::new()->institutional()->withAccess(578, remove: false)->create(['ref_cod_instituicao' => $this->scenario->institution])
    );

    $this->get(($this->edit)($distribution))->assertOk()->assertDontSee("id='btn_excluir'", false);
});
