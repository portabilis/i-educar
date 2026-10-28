<?php

use App\Exceptions\SchoolClass\HasDataInDiario;

test('junta as mensagens dos componentes com lançamentos no iDiário', function () {
    expect((new HasDataInDiario(['Matemática tem notas.', 'História tem faltas.']))->getMessage())
        ->toBe('Matemática tem notas.<br>História tem faltas.');
});
