<?php

namespace iEducar\Modules\Educacenso\Validator;

class NisValidator implements EducacensoValidator
{
    private $message;
    private $nis;

    public function __construct(string $nis)
    {
        $this->nis = $nis;
    }

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        if ($this->isOnlyZeroDigits()) {
            $this->message = 'Os números do campo: NIS (PIS/PASEP) não podem ser todos zeros.';

            return false;
        }

        return true;
    }

    /**
     * @return bool
     */
    private function isOnlyZeroDigits(): bool
    {
        return preg_match('/^(0)\1*$/u', $this->nis);
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }
}
