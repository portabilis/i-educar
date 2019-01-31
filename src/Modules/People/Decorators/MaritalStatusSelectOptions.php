<?php

namespace iEducar\Modules\People\Decorators;

use iEducar\Modules\People\MaritalStatus;

class MaritalStatusSelectOptions
{
    public static function getSelectOptions()
    {
        $options = MaritalStatusSelectOptions::getDescriptiveValues();

        return array_merge(['' => "Selecione"], $options);
    }

    public static function getDescriptiveValues()
    {
        return [
            MaritalStatus::MARRIED => 'Casado(a)',
 			MaritalStatus::COMPANION => 'Companheiro(a)',
 			MaritalStatus::DIVORCED => 'Divorciado(a)',
 			MaritalStatus::SEPARATE => 'Separado(a)',
 			MaritalStatus::NOT_MARRIED => 'Solteiro(a)',
 			MaritalStatus::WIDOWER => 'Viúvo(a)',
        ];
    }

}