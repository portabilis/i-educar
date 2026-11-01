<?php

use App\Models\Enums\ComponentBatchStatus;

test('cada status tem um rótulo e uma cor', function (ComponentBatchStatus $status, string $label, string $color) {
    expect($status->label())->toBe($label)
        ->and($status->color())->toBe($color);
})->with([
    'aguardando' => [ComponentBatchStatus::WAITING, 'Aguardando', 'info'],
    'em execução' => [ComponentBatchStatus::RUNNING, 'Em execução', 'warning'],
    'concluído' => [ComponentBatchStatus::COMPLETED, 'Concluído', 'success'],
    'falhou' => [ComponentBatchStatus::FAILED, 'Falhou', 'danger'],
    'restaurado' => [ComponentBatchStatus::RESTORED, 'Restaurado', 'info'],
]);
