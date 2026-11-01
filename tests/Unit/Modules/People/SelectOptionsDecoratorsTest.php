<?php

use iEducar\Modules\People\Decorators\GenderSelectOptions;
use iEducar\Modules\People\Decorators\MaritalStatusSelectOptions;
use iEducar\Modules\People\Gender;
use iEducar\Modules\People\MaritalStatus;

test('as opções de sexo e estado civil começam pela opção vazia', function () {
    expect(GenderSelectOptions::getSelectOptions())->toBe(['' => 'Sexo', Gender::MALE => 'Masculino', Gender::FEMALE => 'Feminino'])
        ->and(MaritalStatusSelectOptions::getSelectOptions())->toHaveKey('', 'Estado civil')
        ->and(MaritalStatusSelectOptions::getSelectOptions())->toContain('Casado(a)', 'Viúvo(a)', 'Não informado')
        ->and(MaritalStatusSelectOptions::getDescriptiveValues()[MaritalStatus::SINGLE])->toBe('Solteiro(a)');
});
