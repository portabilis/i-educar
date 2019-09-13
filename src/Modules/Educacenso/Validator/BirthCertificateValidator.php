<?php

namespace iEducar\Modules\Educacenso\Validator;

class BirthCertificateValidator implements EducacensoValidator
{
    private $message;
    private $birthCertificate;
    private $birthDate;

    public function __construct($birthCertificate, $birthDate)
    {
        $this->birthCertificate = (string) $birthCertificate;
        $this->birthDate = $birthDate;
    }

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        if (!$this->validateCertificateLength() || !$this->validateCertificateDigits()) {
            $this->message = 'O campo: Tipo certidão civil (novo formato) possui valor inválido';
            return false;
        }

        if (!$this->validateCertificateYear()) {
            $this->message = 'O campo: Tipo certidão civil (novo formato) foi preenchido com o ano inválido. O número localizado nas posições de 11 a 14, não pode ser anterior ao ano de nascimento e nem posterior ao ano corrente';
            return false;
        }

        return true;
    }

    /**
     * @return bool
     */
    public function validateCertificateLength()
    {
        return strlen($this->birthCertificate) == 32;
    }

    /**
     * @return bool
     */
    public function validateCertificateDigits()
    {
        $shouldBeNumericDigits = substr($this->birthCertificate, 0, 30);
        $shouldBeNumericOrXDigits = substr($this->birthCertificate, 30, 30);

        return ctype_digit($shouldBeNumericDigits) && preg_match('/^[0-9, X]*$/', $shouldBeNumericOrXDigits);;
    }

    /**
     * @return bool
     */
    public function validateCertificateYear(): bool
    {
        $certificateYear = $this->certificateYear();

        return $certificateYear >= $this->birthDateYear() && $certificateYear <= date('Y');
    }

    /**
     * @return string
     */
    private function certificateYear()
    {
        return substr($this->birthCertificate, 10, 4);
    }

    /**
     * @return string
     */
    private function birthDateYear()
    {
        return substr($this->birthDate, 0, 4);
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }
}
