<?php

use iEducar\Modules\SchoolClass\Period;

test('os turnos têm nome e sigla', function () {
    expect((new Period)->getDescriptiveValues())->toBe([1 => 'Matutino', 2 => 'Vespertino', 3 => 'Noturno', 4 => 'Integral'])
        ->and(Period::getShortDescriptiveValues())->toBe([1 => 'M', 2 => 'V', 3 => 'N', 4 => 'I']);
});
