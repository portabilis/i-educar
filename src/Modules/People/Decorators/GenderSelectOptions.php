<?php

namespace iEducar\Modules\People\Decorators;

use iEducar\Modules\People\Gender;

class GenderSelectOptions
{
    public static function getSelectOptions()
    {
        $options = GenderSelectOptions::getDescriptiveValues();

        return array_merge(['' => "Sexo"], $options);
    }

    public static function getDescriptiveValues()
    {
        return [
            Gender::MALE => 'Masculino',
            Gender::FEMALE => 'Feminino',
        ];
    }

}