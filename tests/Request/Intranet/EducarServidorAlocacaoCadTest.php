<?php

use App\Models\EmployeeAllocation;
use App\Models\LegacyBondType;
use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacyUserFactory;
use Tests\Request\Support\EmployeeScenario;
use Tests\Request\Support\EnrollmentScenario;
use Tests\Request\Support\LegacyFields;
use Tests\Request\Support\LegacyForm;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);

    $this->school = EnrollmentScenario::for($this->user, 2030);
    $this->institution = $this->school->institution;
    $this->ana = EmployeeScenario::named('Ana Alves', $this->institution, ['carga_horaria' => 40]);
    $this->id = $this->ana->employee->getKey();

    $this->keys = "ref_cod_servidor={$this->id}&ref_cod_instituicao={$this->institution}";
    $this->new = "/intranet/educar_servidor_alocacao_cad.php?{$this->keys}";
    $this->edit = fn ($allocation) => '/intranet/educar_servidor_alocacao_cad.php?cod_servidor_alocacao=' . $allocation->getKey();
    $this->list = "educar_servidor_alocacao_lst.php?{$this->keys}";

    // O que o formulário envia; os rótulos de carga vão em campos ocultos.
    $this->payload = fn (array $overrides = []) => $overrides + [
        'tipoacao' => 'Novo',
        'ref_ref_cod_instituicao' => (string) $this->institution,
        'ref_cod_servidor' => (string) $this->id,
        'cod_servidor_alocacao' => '',
        'carga_horaria_disponivel' => '40:00',
        'ano' => '2030',
        'ref_cod_escola' => (string) $this->school->school->getKey(),
        'periodo' => '1',
        'data_admissao' => '',
        'data_saida' => '',
        'cod_servidor_funcao' => '',
        'ref_cod_funcionario_vinculo' => '',
        'hora_inicial' => '',
        'hora_final' => '',
        'carga_horaria_alocada' => '20:00',
        'hora_atividade' => '',
        'horas_excedentes' => '',
    ];

    $this->allocations = fn () => EmployeeAllocation::query()->where('ref_cod_servidor', $this->id);

    $this->viewer = function (bool $modify, bool $remove) {
        LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $this->institution, 'active_on_ieducar' => true]);

        $this->actingAs(
            LegacyUserFactory::new()->institutional()->withAccess(635, modify: $modify, remove: $remove)->create(['ref_cod_instituicao' => $this->institution])
        );
    };
});

test('abre o cadastro com o servidor, a carga dele, a já alocada no ano corrente, as funções e os vínculos', function () {
    $role = $this->ana->role('Professor', '1001');
    $plain = $this->ana->role('Coordenador', '', teacher: false);
    $bond = LegacyBondType::query()->create(['nm_vinculo' => 'Efetivo', 'abreviatura' => 'EF']);
    $this->ana->allocation($this->school->school, ['ano' => (int) date('Y'), 'carga_horaria' => '12:30:00']);

    $html = $this->get($this->new)->assertOk()->assertSee('Ana Alves')->getContent();
    $fields = LegacyFields::from($html);
    $form = LegacyForm::from($html);

    expect($fields['tipoacao'])->toBe('Novo')
        ->and($fields['ref_cod_servidor'])->toBe((string) $this->id)
        ->and(urldecode($fields['carga_horaria_disponivel']))->toBe('40:00')
        ->and(urldecode($fields['carga_horaria_sem_alocacao']))->toBe('12:30')
        ->and($form->options('cod_servidor_funcao'))->toBe(['' => 'Selecione', (string) $role->getKey() => 'Professor - 1001', (string) $plain->getKey() => 'Coordenador'])
        ->and($form->options('ref_cod_funcionario_vinculo'))->toContain('Efetivo')
        ->and(array_values($form->options('periodo')))->toBe(['Matutino', 'Vespertino', 'Noturno'])
        ->and($bond->exists)->toBeTrue();
});

test('abre uma alocação existente com os dados dela, ainda como cadastro', function () {
    $allocation = $this->ana->allocation($this->school->school, [
        'ano' => 2030,
        'periodo' => 2,
        'carga_horaria' => '20:00:00',
        'hora_inicial' => '13:00',
        'hora_final' => '17:00',
        'hora_atividade' => '02:00',
        'horas_excedentes' => '01:00',
        'data_admissao' => '2030-02-01',
    ]);

    $fields = LegacyFields::from($this->get(($this->edit)($allocation))->assertOk()->assertSee("id='btn_excluir'", false)->getContent());

    expect($fields['tipoacao'])->toBe('Novo')
        ->and($fields['cod_servidor_alocacao'])->toBe((string) $allocation->getKey())
        ->and($fields['ano'])->toBe('2030')
        ->and($fields['periodo'])->toBe('2')
        ->and($fields['ref_cod_escola'])->toBe((string) $this->school->school->getKey())
        ->and($fields['carga_horaria_alocada'])->toBe('20:00')
        ->and($fields['hora_inicial'])->toBe('13:00')
        ->and($fields['hora_final'])->toBe('17:00')
        ->and($fields['hora_atividade'])->toBe('02:00')
        ->and($fields['horas_excedentes'])->toBe('01:00');
});

test('aloca o servidor com todos os campos', function () {
    $role = $this->ana->role('Professor', '1001');
    $bond = LegacyBondType::query()->create(['nm_vinculo' => 'Efetivo', 'abreviatura' => 'EF']);

    $response = $this->post($this->new, ($this->payload)([
        'cod_servidor_funcao' => (string) $role->getKey(),
        'ref_cod_funcionario_vinculo' => (string) $bond->getKey(),
        'data_admissao' => '01/02/2030',
        'data_saida' => '15/12/2030',
        'hora_inicial' => '07:30',
        'hora_final' => '11:30',
        'hora_atividade' => '02:00',
        'horas_excedentes' => '01:00',
    ]));

    $allocation = ($this->allocations)()->sole();

    $response->assertRedirect('educar_servidor_alocacao_det.php?cod_servidor_alocacao=' . $allocation->getKey());

    expect($allocation)
        ->ref_ref_cod_instituicao->toBe($this->institution)
        ->ref_cod_escola->toBe($this->school->school->getKey())
        ->ref_cod_servidor_funcao->toBe($role->getKey())
        ->ref_cod_funcionario_vinculo->toBe($bond->getKey())
        ->ref_usuario_cad->toBe($this->user->getKey())
        ->periodo->toBe(1)
        ->ano->toBe(2030)
        ->carga_horaria->toBe('20:00:00')
        ->hora_inicial->toBe('07:30:00')
        ->hora_final->toBe('11:30:00')
        ->hora_atividade->toBe('02:00:00')
        ->horas_excedentes->toBe('01:00:00')
        ->ativo->toBe(1)
        ->and(substr((string) $allocation->data_admissao, 0, 10))->toBe('2030-02-01')
        ->and(substr((string) $allocation->data_saida, 0, 10))->toBe('2030-12-15');
});

test('gravar a partir de uma alocação existente cria outra e exclui a anterior', function () {
    $old = $this->ana->allocation($this->school->school, ['ano' => 2030, 'periodo' => 1, 'carga_horaria' => '30:00:00']);

    $this->post(($this->edit)($old), ($this->payload)(['cod_servidor_alocacao' => (string) $old->getKey(), 'carga_horaria_alocada' => '36:00']))
        ->assertRedirect();

    expect(EmployeeAllocation::query()->whereKey($old->getKey())->exists())->toBeFalse()
        ->and(($this->allocations)()->sole()->carga_horaria)->toBe('36:00:00');
});

test('recusa o período já alocado na escola e no ano', function () {
    $this->ana->allocation($this->school->school, ['ano' => 2030, 'periodo' => 1, 'carga_horaria' => '10:00:00']);

    $this->post($this->new, ($this->payload)(['carga_horaria_alocada' => '10:00']))
        ->assertOk()
        ->assertSee('Período informado já foi alocado. Por favor, selecione outro.');

    expect(($this->allocations)()->count())->toBe(1);
});

test('recusa alocar mais horas do que a carga do servidor, somando as outras alocações do ano', function () {
    $this->ana->allocation($this->school->school, ['ano' => 2030, 'periodo' => 2, 'carga_horaria' => '30:00:00']);

    $this->post($this->new, ($this->payload)(['carga_horaria_alocada' => '10:30']))
        ->assertOk()
        ->assertSee('Não é possível alocar quantidade superior de horas do que o disponível.');

    expect(($this->allocations)()->count())->toBe(1);

    // Exatamente o que resta cabe.
    $this->post($this->new, ($this->payload)(['carga_horaria_alocada' => '10:00']))->assertRedirect();
});

test('a alocação com data de saída no passado não conta na carga', function () {
    $this->ana->allocation($this->school->school, ['ano' => 2030, 'periodo' => 2, 'carga_horaria' => '40:00:00']);

    $this->post($this->new, ($this->payload)(['carga_horaria_alocada' => '20:00', 'data_saida' => '01/01/2020']))->assertRedirect();
});

test('recusa dados que não permitem gravar', function (array $overrides) {
    $this->post($this->new, ($this->payload)($overrides))
        ->assertOk()
        ->assertSee('Cadastro não realizado.');

    expect(($this->allocations)()->exists())->toBeFalse();
})->with([
    'carga sem o formato de hora' => [['carga_horaria_alocada' => '20']],
    'carga acima de 36 horas' => [['carga_horaria_alocada' => '36:01']],
    'sem período' => [['periodo' => '']],
    'servidor que não é da instituição' => [['ref_ref_cod_instituicao' => '0']],
    'sem servidor' => [['ref_cod_servidor' => '']],
]);

test('defeito: com uma escola que não existe o formulário recusado estoura', function () {
    // A gravação é recusada, mas o campo de escola não sabe se redesenhar
    // com um código que não existe.
    $this->post($this->new, ($this->payload)(['ref_cod_escola' => '999999999']))->assertStatus(500);
});

test('a ação de editar não existe', function () {
    $this->post($this->new, ($this->payload)(['tipoacao' => 'Editar']))
        ->assertOk()
        ->assertSee('Não foi possível editar a informação. [CAD02]');
});

test('exclui a alocação', function () {
    $allocation = $this->ana->allocation($this->school->school);

    $this->post(($this->edit)($allocation), ($this->payload)(['tipoacao' => 'Excluir', 'cod_servidor_alocacao' => (string) $allocation->getKey()]))
        ->assertRedirect($this->list)
        ->assertSessionHas('legacy', 'Exclusão efetuada com sucesso.<br>');

    expect(EmployeeAllocation::query()->whereKey($allocation->getKey())->exists())->toBeFalse();
});

test('recusa excluir sem o código da alocação', function () {
    $this->post($this->new, ($this->payload)(['tipoacao' => 'Excluir']))
        ->assertOk()
        ->assertSee('Exclusão não realizada.');
});

test('quem só pode visualizar não abre nem grava', function () {
    ($this->viewer)(false, false);

    $this->get($this->new)->assertRedirect('educar_servidor_lst.php');
    $this->post($this->new, ($this->payload)())->assertRedirect($this->list);

    expect(($this->allocations)()->exists())->toBeFalse();
});

test('defeito: quem só pode visualizar exclui a alocação pelo envio direto', function () {
    // A exclusão não confere a permissão.
    $allocation = $this->ana->allocation($this->school->school);

    ($this->viewer)(false, false);

    $this->post(($this->edit)($allocation), ($this->payload)(['tipoacao' => 'Excluir', 'cod_servidor_alocacao' => (string) $allocation->getKey()]))
        ->assertRedirect($this->list);

    expect(EmployeeAllocation::query()->whereKey($allocation->getKey())->exists())->toBeFalse();
});

test('quem não pode remover não vê o botão de excluir', function () {
    $allocation = $this->ana->allocation($this->school->school);

    ($this->viewer)(true, false);

    $this->get(($this->edit)($allocation))->assertOk()->assertDontSee("id='btn_excluir'", false);
});

test('volta para a listagem de servidores sem servidor nem alocação na URL', function () {
    $this->get('/intranet/educar_servidor_alocacao_cad.php')->assertRedirect('educar_servidor_lst.php');
});

test('com uma alocação inexistente abre o formulário sem servidor', function () {
    $this->get('/intranet/educar_servidor_alocacao_cad.php?cod_servidor_alocacao=999999999')->assertOk()->assertDontSee('Ana Alves');
});
