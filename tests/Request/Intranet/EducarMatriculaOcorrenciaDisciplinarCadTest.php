<?php

use App\Models\LegacyRegistrationDisciplinaryOccurrenceType;
use Database\Factories\LegacyDisciplinaryOccurrenceTypeFactory;
use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacyRegistrationDisciplinaryOccurrenceTypeFactory;
use Database\Factories\LegacyUserFactory;
use Tests\Request\Support\EnrollmentScenario;
use Tests\Request\Support\LegacyFields;
use Tests\Request\Support\LegacyForm;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);

    $this->scenario = EnrollmentScenario::for($this->user, 2030);
    $this->registration = $this->scenario->registration(['ref_cod_aluno' => $this->scenario->student('Ana Alves')]);

    $this->type = fn (string $name) => LegacyDisciplinaryOccurrenceTypeFactory::new()->create(['institution_id' => $this->scenario->institution, 'name' => $name]);
    $this->delay = ($this->type)('Atraso');
    $this->fight = ($this->type)('Briga');

    $this->new = '/intranet/educar_matricula_ocorrencia_disciplinar_cad.php?ref_cod_matricula=' . $this->registration->getKey();
    $this->list = 'educar_matricula_ocorrencia_disciplinar_lst.php?ref_cod_matricula=' . $this->registration->getKey();

    $this->occurrence = fn (array $attributes = []) => LegacyRegistrationDisciplinaryOccurrenceTypeFactory::new()->create($attributes + [
        'ref_cod_matricula' => $this->registration,
        'ref_cod_tipo_ocorrencia_disciplinar' => $this->delay,
        'observacao' => 'Chegou depois do sinal',
        'data_cadastro' => '2030-03-10 07:45:00',
        'visivel_pais' => 0,
    ])->refresh();

    $this->edit = fn ($occurrence) => $this->new . "&ref_cod_tipo_ocorrencia_disciplinar={$occurrence->ref_cod_tipo_ocorrencia_disciplinar}&sequencial={$occurrence->sequencial}";

    $this->saved = fn () => LegacyRegistrationDisciplinaryOccurrenceType::query()->where('ref_cod_matricula', $this->registration->getKey())->orderBy('cod_ocorrencia_disciplinar')->get();
});

test('abre o cadastro com o aluno, os tipos ativos em ordem de nome e a data e hora de agora', function () {
    ($this->type)('Tipo extinto')->update(['ativo' => 0]);

    $html = $this->get($this->new)->assertOk()->assertSee('Ana Alves')->getContent();
    $fields = LegacyFields::from($html);

    expect($fields['tipoacao'])->toBe('Novo')
        ->and($fields['ref_cod_matricula'])->toBe((string) $this->registration->getKey())
        ->and($fields['data_cadastro'])->toBe(now()->format('d/m/Y'))
        ->and($fields['hora_cadastro'])->toMatch('/^\d\d:\d\d$/')
        ->and(array_values(LegacyForm::from($html)->options('ref_cod_tipo_ocorrencia_disciplinar')))->toBe(['Selecione', 'Atraso', 'Briga']);
});

test('sem matrícula na URL abre a busca de matrícula', function () {
    $fields = LegacyFields::from($this->get('/intranet/educar_matricula_ocorrencia_disciplinar_cad.php')->assertOk()->getContent());

    expect($fields['tipoacao'])->toBe('Novo')
        ->and($fields)->toHaveKey('matricula_id');
});

test('registra a ocorrência com data, hora, tipo, observação e visibilidade, numerando em sequência por tipo', function () {
    ($this->occurrence)();
    ($this->occurrence)(['ref_cod_tipo_ocorrencia_disciplinar' => $this->fight]);

    $payload = LegacyFields::from($this->get($this->new)->assertOk()->getContent(), [
        'ref_cod_tipo_ocorrencia_disciplinar' => $this->fight->getKey(),
        'data_cadastro' => '12/03/2030',
        'hora_cadastro' => '10:15',
        'observacao' => 'Brigou no recreio',
        'visivel_pais' => 'on',
    ]);

    $this->post($this->new, $payload)->assertRedirect($this->list);

    $occurrence = ($this->saved)()->last();

    expect($occurrence)
        ->ref_cod_tipo_ocorrencia_disciplinar->toBe($this->fight->getKey())
        ->observacao->toBe('Brigou no recreio')
        ->sequencial->toBe(2)
        ->ativo->toBe(1)
        ->ref_usuario_cad->toBe($this->user->getKey())
        ->and((bool) $occurrence->visivel_pais)->toBeTrue()
        ->and($occurrence->data_cadastro->format('Y-m-d H:i'))->toBe('2030-03-12 10:15');
});

test('registra pela busca de matrícula e fica na tela para o próximo lançamento', function () {
    $url = '/intranet/educar_matricula_ocorrencia_disciplinar_cad.php';

    $payload = LegacyFields::from($this->get($url)->assertOk()->getContent(), [
        'matricula_id' => $this->registration->getKey(),
        'ref_cod_tipo_ocorrencia_disciplinar' => $this->delay->getKey(),
        'data_cadastro' => '12/03/2030',
        'hora_cadastro' => '07:50',
        'observacao' => 'Chegou atrasado',
    ]);

    $this->post($url, $payload)->assertOk()->assertSee('Cadastro efetuado com sucesso.', false);

    $occurrence = ($this->saved)()->sole();

    expect($occurrence->observacao)->toBe('Chegou atrasado')
        ->and((bool) $occurrence->visivel_pais)->toBeFalse();
});

test('recusa o cadastro sem matrícula', function () {
    $url = '/intranet/educar_matricula_ocorrencia_disciplinar_cad.php';

    $payload = LegacyFields::from($this->get($url)->assertOk()->getContent(), [
        'ref_cod_tipo_ocorrencia_disciplinar' => $this->delay->getKey(),
        'observacao' => 'Sem matrícula',
    ]);

    $this->post($url, $payload)->assertSessionHasErrors(['ref_cod_matricula' => 'Cadastro não efetuado. A matrícula é inválida!']);

    expect(($this->saved)())->toBeEmpty();
});

test('abre a edição com os dados da ocorrência', function () {
    $occurrence = ($this->occurrence)(['visivel_pais' => 1]);

    $fields = LegacyFields::from($this->get(($this->edit)($occurrence))->assertOk()->assertSee('Ana Alves')->getContent());

    expect($fields['tipoacao'])->toBe('Editar')
        ->and($fields['cod_ocorrencia_disciplinar'])->toBe((string) $occurrence->getKey())
        ->and($fields['ref_cod_tipo_ocorrencia_disciplinar'])->toBe((string) $this->delay->getKey())
        ->and($fields['data_cadastro'])->toBe('10/03/2030')
        ->and($fields['hora_cadastro'])->toBe('07:45')
        ->and($fields['observacao'])->toBe('Chegou depois do sinal')
        ->and($fields)->toHaveKey('visivel_pais');
});

test('edita a ocorrência', function () {
    $occurrence = ($this->occurrence)(['visivel_pais' => 1]);

    $payload = LegacyFields::from($this->get(($this->edit)($occurrence))->assertOk()->getContent(), [
        'ref_cod_tipo_ocorrencia_disciplinar' => $this->fight->getKey(),
        'data_cadastro' => '11/03/2030',
        'hora_cadastro' => '09:30',
        'observacao' => 'Foi uma briga, não um atraso',
    ]);

    unset($payload['visivel_pais']);

    $this->post(($this->edit)($occurrence), $payload)->assertRedirect($this->list);

    expect($occurrence->refresh())
        ->ref_cod_tipo_ocorrencia_disciplinar->toBe($this->fight->getKey())
        ->observacao->toBe('Foi uma briga, não um atraso')
        ->ref_usuario_exc->toBe($this->user->getKey())
        ->and((bool) $occurrence->visivel_pais)->toBeFalse()
        ->and($occurrence->data_cadastro->format('Y-m-d H:i'))->toBe('2030-03-11 09:30');
});

test('edita pela busca de matrícula e volta para o cadastro', function () {
    $occurrence = ($this->occurrence)();

    $payload = LegacyFields::from($this->get(($this->edit)($occurrence))->assertOk()->getContent(), [
        'ref_cod_matricula' => '',
        'matricula_id' => $this->registration->getKey(),
        'observacao' => 'Observação corrigida',
    ]);

    $this->post(($this->edit)($occurrence), $payload)->assertRedirect('educar_matricula_ocorrencia_disciplinar_cad.php');

    expect($occurrence->refresh()->observacao)->toBe('Observação corrigida');
});

// Com a integração configurada, a ocorrência visível aos pais é enviada ao
// sistema externo. O endereço daqui é uma porta fechada da própria máquina: a
// conexão é recusada na hora, e a tela segue como se o envio tivesse dado certo.
test('a ocorrência visível aos pais é gravada mesmo quando o sistema externo não responde', function () {
    LegacyGeneralConfigurationFactory::new()->create([
        'ref_cod_instituicao' => $this->scenario->institution,
        'active_on_ieducar' => true,
        'url_novo_educacao' => 'http://127.0.0.1:9',
    ]);

    $payload = LegacyFields::from($this->get($this->new)->assertOk()->getContent(), [
        'ref_cod_tipo_ocorrencia_disciplinar' => $this->delay->getKey(),
        'observacao' => 'Chegou atrasado',
        'visivel_pais' => 'on',
    ]);

    $this->post($this->new, $payload)->assertRedirect($this->list);

    expect((bool) ($this->saved)()->sole()->visivel_pais)->toBeTrue();
});

test('exclui a ocorrência desativando o registro', function () {
    $occurrence = ($this->occurrence)();

    $payload = LegacyFields::from($this->get(($this->edit)($occurrence))->assertOk()->getContent(), ['tipoacao' => 'Excluir']);

    $this->post(($this->edit)($occurrence), $payload)->assertRedirect($this->list);

    expect($occurrence->refresh())
        ->ativo->toBe(0)
        ->ref_usuario_exc->toBe($this->user->getKey());
});

test('com tipo e sequência que não existem abre como cadastro', function () {
    $fields = LegacyFields::from($this->get($this->new . '&ref_cod_tipo_ocorrencia_disciplinar=' . $this->delay->getKey() . '&sequencial=99')->assertOk()->getContent());

    expect($fields['tipoacao'])->toBe('Novo');
});

test('volta para a listagem de matrículas quando o usuário não pode cadastrar ocorrências', function () {
    LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $this->scenario->institution, 'active_on_ieducar' => true]);

    $this->actingAs(
        LegacyUserFactory::new()->institutional()->withAccess(578)->withAccess(21145, modify: false, remove: false)
            ->create(['ref_cod_instituicao' => $this->scenario->institution])
    );

    $this->get($this->new)->assertRedirect('educar_matricula_lst.php');
});
