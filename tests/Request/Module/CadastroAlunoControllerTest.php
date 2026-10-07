<?php

use Database\Factories\LegacyStudentFactory;
use Database\Factories\LegacyUserFactory;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    $this->student = LegacyStudentFactory::new()->create();
});

test('abre o formulário de um novo aluno', function () {
    $this->get('/module/Cadastro/aluno')->assertOk();
});

test('abre o formulário de edição do aluno', function () {
    $this->get('/module/Cadastro/aluno?id=' . $this->student->getKey())->assertOk();
});

test('abre o formulário quando o id não corresponde a um aluno', function () {
    $this->get('/module/Cadastro/aluno?id=' . ($this->student->getKey() + 1000))->assertOk();
});

test('abre o formulário quando o id não é um inteiro', function (string $suffix) {
    $this->get('/module/Cadastro/aluno?id=' . $this->student->getKey() . $suffix)->assertOk();
})->with(['.', '.5', 'e3']);
