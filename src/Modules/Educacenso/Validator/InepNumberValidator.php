<?php

namespace iEducar\Modules\Educacenso\Validator;

class InepNumberValidator implements EducacensoValidator
{
    private $number;

    public function __construct($number)
    {
        $this->number = $number;
    }

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        if (!$this->number) {
            return true;
        }

        return strlen($this->number) == 12;
    }

    public function getMessage()
    {
        return 'Verifique se o INEP possui 12 dígitos';
    }
}
