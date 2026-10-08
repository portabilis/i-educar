<?php

use Database\Factories\LegacyUserFactory;
use Tests\Request\Support\EnrollmentScenario;
use Tests\Request\Support\LegacyFields;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);

    $this->scenario = EnrollmentScenario::for($this->user, 2030);
    $this->schoolClass = $this->scenario->schoolClass(['nm_turma' => 'Turma Alfa']);
    $this->url = '/intranet/educar_ordenar_alunos_turma.php?cod_turma=' . $this->schoolClass->getKey();
});

test('lista os alunos da turma pela sequência de fechamento e depois pelo nome', function () {
    $carla = $this->scenario->enrolled('Carla Campos', $this->schoolClass, ['sequencial_fechamento' => 1]);
    $bruno = $this->scenario->enrolled('Bruno Barros', $this->schoolClass, ['sequencial_fechamento' => 2]);
    $ana = $this->scenario->enrolled('Ana Alves', $this->schoolClass, ['sequencial_fechamento' => 2]);

    $response = $this->get($this->url)->assertOk()->assertSeeInOrder(['Carla Campos', 'Ana Alves', 'Bruno Barros']);
    $fields = LegacyFields::from($response->getContent());

    expect($fields['tipoacao'])->toBe('Editar')
        ->and($fields['cod_turma'])->toBe((string) $this->schoolClass->getKey())
        ->and($fields['sequencia'])->toBe([$carla->getKey() => '1', $ana->getKey() => '2', $bruno->getKey() => '2']);
});

test('grava a sequência informada para cada aluno e volta para a turma', function () {
    $ana = $this->scenario->enrolled('Ana Alves', $this->schoolClass, ['sequencial_fechamento' => 1]);
    $bruno = $this->scenario->enrolled('Bruno Barros', $this->schoolClass, ['sequencial_fechamento' => 2]);

    $payload = LegacyFields::from($this->get($this->url)->assertOk()->getContent(), [
        'sequencia' => [$ana->getKey() => 2, $bruno->getKey() => 1],
    ]);

    $this->post($this->url, $payload)->assertRedirect('educar_turma_det.php?cod_turma=' . $this->schoolClass->getKey());

    expect($ana->refresh()->sequencial_fechamento)->toBe(2)
        ->and($bruno->refresh()->sequencial_fechamento)->toBe(1);
});

test('recusa quando algum aluno fica sem sequência e não grava nenhum', function () {
    $ana = $this->scenario->enrolled('Ana Alves', $this->schoolClass, ['sequencial_fechamento' => 1]);
    $bruno = $this->scenario->enrolled('Bruno Barros', $this->schoolClass, ['sequencial_fechamento' => 2]);

    $payload = LegacyFields::from($this->get($this->url)->assertOk()->getContent(), [
        'sequencia' => [$ana->getKey() => 5, $bruno->getKey() => ''],
    ]);

    $this->post($this->url, $payload)
        ->assertOk()
        ->assertSee('Edição não realizada. É necessário preencher a sequência de todos os alunos.');

    expect($ana->refresh()->sequencial_fechamento)->toBe(1)
        ->and($bruno->refresh()->sequencial_fechamento)->toBe(2);
});

test('sem alunos na turma a tela abre vazia e não grava nada', function () {
    $fields = LegacyFields::from($this->get($this->url)->assertOk()->getContent());

    expect($fields['tipoacao'])->toBe('Novo')
        ->and($fields)->not->toHaveKey('sequencia');

    $this->post($this->url, $fields)->assertOk();
});

test('a tela não exclui nada', function () {
    $ana = $this->scenario->enrolled('Ana Alves', $this->schoolClass, ['sequencial_fechamento' => 1]);

    $payload = LegacyFields::from($this->get($this->url)->assertOk()->getContent(), ['tipoacao' => 'Excluir']);

    $this->post($this->url, $payload)->assertOk();

    expect($ana->refresh()->sequencial_fechamento)->toBe(1);
});

test('sem turma informada a tela abre sem campos', function () {
    $fields = LegacyFields::from($this->get('/intranet/educar_ordenar_alunos_turma.php')->assertOk()->getContent());

    expect($fields)->not->toHaveKey('cod_turma');
});
