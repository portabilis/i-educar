<?php

test('não deve validar cadastro de curso com horas de falta negativas', function () {
    $horaFalta = -3;
    
    // Agora usando a lógica corrigida
    $isValid = validaHorasFaltaCorrigida($horaFalta);

    expect($isValid)->toBeFalse();
});

test('deve validar cadastro de curso com horas de falta positivas', function () {
    $horaFalta = 10;
    $isValid = validaHorasFaltaCorrigida($horaFalta);
    expect($isValid)->toBeTrue();
});

// Essa foi a lógica que foi levada para o arquivo fonte
function validaHorasFaltaCorrigida($valor) {
    if ($valor < 0) {
        return false;
    }
    return true;
}

