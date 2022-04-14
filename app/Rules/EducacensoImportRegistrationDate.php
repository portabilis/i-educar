<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class EducacensoImportRegistrationDate implements Rule
{
    public $selectedYear;

    public function __construct($selectedYear)
    {
        $this->selectedYear = $selectedYear;
    }

    /**
     * Verifica se o ano da data de entrada da matrícula é
     * menor ou igual ao ano selecionado no filtro
     *
     * @param string $attribute
     * @param mixed  $value
     *
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $registrationYear = \DateTime::createFromFormat('d/m/Y', $value)->format('Y');

        return $registrationYear <= $this->selectedYear;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'O ano da data de entrada das matrículas não pode ser maior que o ano selecionado';
    }
}
