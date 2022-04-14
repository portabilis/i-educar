<?php

namespace iEducar\Modules\Educacenso\Validator;

class BirthDateValidator implements EducacensoValidator
{
    private $message;
    private $birthDate;

    public function __construct(string $birthDate)
    {
        $this->birthDate = $birthDate;
    }

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        if ($this->birthDateGreaterThanToday()) {
            $this->message = 'Informe uma data de nascimento menor que o dia de hoje.';

            return false;
        }

        return true;
    }

    /**
     * @return bool
     */
    private function birthDateGreaterThanToday(): bool
    {
        return $this->birthDate > date('Y-m-d');
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }
}
