<?php

namespace iEducar\Modules\People\Decorators;

use iEducar\Modules\People\MaritalStatus;

class MaritalStatusSelectOptions
{
    public static function getSelectOptions()
    {
        $options = MaritalStatusSelectOptions::getDescriptiveValues();

        return array_merge(['' => "Estado civil"], $options);
    }

    public static function getDescriptiveValues()
    {
        return [
            MaritalStatus::MARRIED => 'Casado(a)',
            MaritalStatus::COMPANION => 'Companheiro(a)',
            MaritalStatus::DIVORCED => 'Divorciado(a)',
            MaritalStatus::SEPARATED => 'Separado(a)',
            MaritalStatus::SINGLE => 'Solteiro(a)',
            MaritalStatus::WIDOWER => 'Viúvo(a)',
        ];
    }

}