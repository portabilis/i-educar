<?php

use App\Models\ManagerAccessCriteria;
use iEducar\Modules\School\Model\ActiveLooking;
use iEducar\Modules\Transport\Period;
use iEducar\Support\View\SelectOptions;
use Tests\TestCase;

uses(TestCase::class);

test('as listas de opções trazem a opção vazia e os valores de cada tabela', function () {
    expect(SelectOptions::transportPeriods())->toBe(['' => 'Selecione'] + Period::getDescriptiveValues())
        ->and(SelectOptions::transportPeriods())->toHaveKey(Period::AFTERNOON_NIGHT, 'Vespertino e noturno')
        ->and(SelectOptions::schoolManagerAccessCriterias())->toHaveCount(ManagerAccessCriteria::query()->count() + 1)
        ->and(SelectOptions::schoolManagerLinkTypes())->toHaveKey('', 'Selecione')
        ->and(SelectOptions::employeeGraduationDisciplines())->toHaveKey('', 'Selecione')
        ->and(SelectOptions::zonasLocalizacao())->toHaveKey('', 'Selecione')
        ->and(SelectOptions::localizacaoDiferencia())->toHaveKey('', 'Selecione')
        ->and(SelectOptions::formats())->toBe(['csv' => 'CSV', 'pdf' => 'PDF'])
        ->and(ActiveLooking::getShortDescriptiveValues()[ActiveLooking::ACTIVE_LOOKING_WITH_ABSENCE_RESULT])->toBe('Retorno c/ AJ');
});
