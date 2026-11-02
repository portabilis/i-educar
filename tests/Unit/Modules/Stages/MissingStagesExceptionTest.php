<?php

use iEducar\Modules\Stages\Exceptions\MissingStagesException;
use iEducar\Modules\Stages\Exceptions\StagesNotInformedByCoordinatorException;

test('a falta de etapas tem código padrão e, para o coordenador, mensagem e código próprios', function () {
    $coordinator = new StagesNotInformedByCoordinatorException([1, 2], 'Bimestre');

    expect((new MissingStagesException([1], 'Bimestre'))->getCode())->toBe(MissingStagesException::DEFAULT_ERROR)
        ->and($coordinator->getMessage())->toBe('O secretário/coordenador deve lançar as notas das etapas: 1Bimestre, 2 Bimestre.')
        ->and($coordinator->getCode())->toBe(MissingStagesException::COORDINATOR_ERROR);
});
