<?php

use App\Support\View\NotFoundMessage;
use PHPUnit\Framework\TestCase;

uses(TestCase::class);

test('monta mensagem com codigo', function () {
    $this->assertSame(
        'Aluno não encontrado. O código 999999 não existe ou foi excluído. Use a busca abaixo para localizá-lo.',
        NotFoundMessage::make(registro: 'aluno', codigo: '999999')
    );
});

test('concorda com registro feminino', function () {
    $this->assertSame(
        'Matrícula não encontrada. O código 10 não existe ou foi excluído. Use a busca abaixo para localizá-la.',
        NotFoundMessage::make(registro: 'matrícula', feminino: true, codigo: 10)
    );
});

test('omite codigo ausente ou invalido', function () {
    $esperado = 'Curso não encontrado. O registro não existe ou foi excluído. Use a busca abaixo para localizá-lo.';

    foreach ([null, '', 0, '0', '-5', 'abc', '12abc'] as $codigo) {
        $this->assertSame($esperado, NotFoundMessage::make(registro: 'curso', codigo: $codigo));
    }
});

test('aceita motivo e dica personalizados', function () {
    $this->assertSame(
        'Turma não encontrada. A turma não existe. Confira a listagem.',
        NotFoundMessage::make(registro: 'turma', feminino: true, codigo: 1, motivo: 'A turma não existe.', dica: 'Confira a listagem.')
    );
});

test('escapa html', function () {
    $mensagem = NotFoundMessage::make(registro: '<b>aluno</b>', codigo: '<script>alert(1)</script>');

    $this->assertStringNotContainsString('<', $mensagem);
    $this->assertStringContainsString('&lt;b&gt;aluno&lt;/b&gt;', $mensagem);
});
