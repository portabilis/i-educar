<?php

use Tests\TestCase;

uses(TestCase::class);

it('falha na validação quando aluno não possui CPF e não informa ID INEP', function () {
    $controller = new AlunoController();

    
    $requestData = [
        'possui_cpf' => 0,
        'aluno_inep_id' => null,
        'pessoa_id' => 123
    ];


    $controller->getRequest()->merge($requestData);


    expect($controller->commonValidate())->beFalse();
});

it('valida com sucesso quando aluno não possui CPF mas informa ID INEP válido', function () {
    $controller = new AlunoController();

    $requestData = [
        'possui_cpf' => 0,
        'aluno_inep_id' => '123456789012',
        'pessoa_id' => 123
    ];

    $controller->getRequest()->merge($requestData);

    expect($controller->commonValidate())->beTrue();
});