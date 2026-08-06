<?php

namespace iEducar\Modules\Educacenso\Validator;

class ResidenceCityValidator implements EducacensoValidator
{
    private $message;

    private $postalCode;

    private $cityId;

    public function __construct($postalCode, $cityId)
    {
        $this->postalCode = $postalCode;
        $this->cityId = $cityId;
    }

    public function isValid(): bool
    {
        if (!empty($this->postalCode) && empty($this->cityId)) {
            $this->message = 'O campo Município de residência deve ser preenchido quando o CEP for informado.';

            return false;
        }

        return true;
    }

    public function getMessage()
    {
        return $this->message;
    }
}
