<?php

use App\Rules\CanChangeExitDate;

/**
 * Chama o método privado removeIgnoredTypes().
 *
 * Os avisos do PHP (Undefined array key, Undefined property) são convertidos em
 * exceção durante a chamada, igual ao comportamento do Laravel, para que uma
 * entrada que gere aviso faça o teste falhar em qualquer ambiente.
 */
function callRemoveIgnoredTypes(array $studentActivity): array
{
    set_error_handler(function (int $level, string $message): never {
        throw new ErrorException($message, 0, $level);
    });

    try {
        $method = new ReflectionMethod(CanChangeExitDate::class, 'removeIgnoredTypes');

        return $method->invoke(new CanChangeExitDate, $studentActivity);
    } finally {
        restore_error_handler();
    }
}

/**
 * Monta a resposta do i-Diário: cada item é um objeto com a propriedade type.
 */
function studentActivityOf(array $types): array
{
    $items = array_map(fn ($type) => (object) ['type' => $type], $types);

    return ['student_activity' => $items];
}

/**
 * Tipos que sobraram na lista, sem considerar as chaves (unset não reindexa).
 */
function remainingTypes(array $result): array
{
    $types = array_map(fn ($item) => $item->type, $result['student_activity']);

    return array_values($types);
}

// Casos 1 a 8: classes de equivalência válidas e valores limite (caixa preta)
// e cobertura de comandos, decisões e condições (caixa branca).
it('removes ignored types and keeps the others', function (array $types, array $expected) {
    $result = callRemoveIgnoredTypes(studentActivityOf($types));

    expect(remainingTypes($result))->toBe($expected);
})->with([
    'case 1: empty list' => [[], []],
    'case 2: only descriptive_exam' => [['descriptive_exam'], []],
    'case 3: only general_descriptive_exam' => [['general_descriptive_exam'], []],
    'case 4: only a kept type' => [['daily_note'], ['daily_note']],
    'case 5: ignored type in the first position' => [['descriptive_exam', 'daily_note'], ['daily_note']],
    'case 6: ignored type in the last position' => [['daily_note', 'general_descriptive_exam'], ['daily_note']],
    'case 7: both ignored types' => [['descriptive_exam', 'general_descriptive_exam'], []],
    'case 8: more than one kept type' => [['daily_note', 'transfer_note'], ['daily_note', 'transfer_note']],
]);

it('keeps the other keys of the response untouched', function () {
    $response = studentActivityOf(['descriptive_exam', 'daily_note']);
    $response['other_key'] = 'value';

    $result = callRemoveIgnoredTypes($response);

    expect($result['other_key'])->toBe('value');
});

// Caso 9: classe inválida CE2 (array sem a chave student_activity).
it('returns an empty list when the student_activity key is missing', function () {
    $result = callRemoveIgnoredTypes(['other_key' => 'value']);

    expect($result['student_activity'])->toBe([]);
});

// Caso 10: classe inválida CE6 (tipo desconhecido). Hoje já é mantido.
it('keeps an item with an unknown type', function () {
    $result = callRemoveIgnoredTypes(studentActivityOf(['unknown_type']));

    expect(remainingTypes($result))->toBe(['unknown_type']);
});

// Caso 11: classe inválida CE7 (item sem a propriedade type).
it('keeps an item without the type property', function () {
    $result = callRemoveIgnoredTypes(['student_activity' => [(object) []]]);

    expect($result['student_activity'])->toHaveCount(1);
});

// Caso 12: in_array sem strict considera true == 'descriptive_exam'.
it('does not remove a non-string type that only loosely equals an ignored type', function () {
    $result = callRemoveIgnoredTypes(studentActivityOf([true]));

    expect($result['student_activity'])->toHaveCount(1);
});