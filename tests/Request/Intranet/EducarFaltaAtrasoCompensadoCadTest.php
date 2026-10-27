<?php

use App\Models\LegacyAbsenceDelayCompensate;
use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacyUserFactory;
use Tests\Request\Support\EmployeeScenario;
use Tests\Request\Support\EnrollmentScenario;
use Tests\Request\Support\LegacyFields;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);

    $this->school = EnrollmentScenario::for($this->user, 2030);
    $this->institution = $this->school->institution;
    $this->schoolId = $this->school->school->getKey();

    $this->ana = EmployeeScenario::named('Ana Alves', $this->institution);
    $this->id = $this->ana->employee->getKey();

    $this->detail = "educar_falta_atraso_det.php?ref_cod_servidor={$this->id}&ref_cod_escola={$this->schoolId}&ref_cod_instituicao={$this->institution}";
    $this->new = "/intranet/educar_falta_atraso_compensado_cad.php?ref_cod_servidor={$this->id}&ref_cod_escola={$this->schoolId}&ref_cod_instituicao={$this->institution}";
    $this->edit = fn (LegacyAbsenceDelayCompensate $compensation) => "{$this->new}&cod_compensado={$compensation->getKey()}";

    $this->compensations = fn () => LegacyAbsenceDelayCompensate::query()->where('ref_cod_servidor', $this->id);

    $this->viewer = function () {
        LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $this->institution, 'active_on_ieducar' => true]);

        $this->actingAs(
            LegacyUserFactory::new()->institutional()->withAccess(635, modify: false, remove: false)->create(['ref_cod_instituicao' => $this->institution])
        );
    };
});

test('abre o cadastro com a escola e a instituição do servidor', function () {
    $html = $this->get($this->new)->assertOk()->getContent();
    $fields = LegacyFields::from($html);

    expect($fields)->toMatchArray([
        'tipoacao' => 'Novo',
        'ref_cod_servidor' => (string) $this->id,
        'ref_cod_instituicao' => (string) $this->institution,
        'ref_cod_escola' => (string) $this->schoolId,
        'data_inicio' => '',
        'data_fim' => '',
    ])
        ->and($html)->toContain($this->detail)
        ->not->toContain("id='btn_excluir'");
});

test('cadastra o período compensado', function () {
    $payload = LegacyFields::from($this->get($this->new)->assertOk()->getContent(), ['data_inicio' => '12/03/2030', 'data_fim' => '14/03/2030']);

    $this->post($this->new, $payload)->assertRedirect($this->detail);

    expect(($this->compensations)()->sole()->getAttributes())->toMatchArray([
        'ref_cod_escola' => $this->schoolId,
        'ref_ref_cod_instituicao' => $this->institution,
        'data_inicio' => '2030-03-12 00:00:00',
        'data_fim' => '2030-03-14 00:00:00',
        'ref_usuario_cad' => $this->user->getKey(),
        'ref_usuario_exc' => null,
    ]);
});

test('recusa o cadastro sem escola', function () {
    $payload = LegacyFields::from($this->get($this->new)->assertOk()->getContent(), ['data_inicio' => '12/03/2030', 'data_fim' => '14/03/2030']);
    unset($payload['ref_cod_escola']);

    $this->post($this->new, $payload)->assertOk()->assertSee('O campo escola deve ser preenchido.');

    expect(($this->compensations)()->count())->toBe(0);
});

test('abre a edição com as datas gravadas e o botão de excluir', function () {
    $compensation = $this->ana->compensation($this->school->school, '2030-03-12', '2030-03-14');

    $html = $this->get(($this->edit)($compensation))->assertOk()->getContent();

    expect(LegacyFields::from($html))->toMatchArray([
        'tipoacao' => 'Editar',
        'cod_compensado' => (string) $compensation->getKey(),
        'data_inicio' => '12/03/2030',
        'data_fim' => '14/03/2030',
    ])
        ->and($html)->toContain("id='btn_excluir'");
});

test('edita o período compensado', function () {
    $compensation = $this->ana->compensation($this->school->school, '2030-03-12', '2030-03-14');

    $payload = LegacyFields::from($this->get(($this->edit)($compensation))->assertOk()->getContent(), ['data_fim' => '20/03/2030']);

    $this->post(($this->edit)($compensation), $payload)->assertRedirect($this->detail);

    expect($compensation->refresh()->getAttributes())->toMatchArray([
        'data_inicio' => '2030-03-12 00:00:00',
        'data_fim' => '2030-03-20 00:00:00',
        'ref_usuario_exc' => $this->user->getKey(),
    ]);
});

test('exclui o período compensado', function () {
    $compensation = $this->ana->compensation($this->school->school, '2030-03-12', '2030-03-14');

    $payload = LegacyFields::from($this->get(($this->edit)($compensation))->assertOk()->getContent(), ['tipoacao' => 'Excluir']);

    $this->post(($this->edit)($compensation), $payload)->assertRedirect($this->detail);

    expect(($this->compensations)()->count())->toBe(0);
});

test('quem só pode visualizar volta para o detalhe ao abrir, gravar, editar ou excluir', function () {
    $compensation = $this->ana->compensation($this->school->school, '2030-03-12', '2030-03-14');
    $new = LegacyFields::from($this->get($this->new)->assertOk()->getContent(), ['data_inicio' => '01/04/2030', 'data_fim' => '02/04/2030']);
    $edit = LegacyFields::from($this->get(($this->edit)($compensation))->assertOk()->getContent(), ['data_fim' => '20/03/2030']);

    ($this->viewer)();

    $this->get($this->new)->assertRedirect($this->detail);
    $this->post($this->new, $new)->assertRedirect($this->detail);
    $this->post(($this->edit)($compensation), $edit)->assertRedirect($this->detail);
    $this->post(($this->edit)($compensation), ['tipoacao' => 'Excluir'] + $edit)->assertRedirect($this->detail);

    expect(($this->compensations)()->sole()->getRawOriginal('data_fim'))->toBe('2030-03-14 00:00:00');
});
