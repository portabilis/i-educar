<?php

namespace App\Rules;

use App\Models\LegacyStudent;
use Illuminate\Contracts\Validation\Rule;

class StateRegistrationUniqueRule implements Rule
{
    private $studentToIgnore;

    /**
     * Create a new rule instance.
     *
     * @param LegacyStudent $studentToIgnore
     */
    public function __construct($studentToIgnore = null)
    {
        $this->studentToIgnore = $studentToIgnore;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed  $value
     *
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return LegacyStudent::query()
            ->where('aluno_estado_id', $value)
            ->when($this->studentToIgnore, function ($query) use ($value) {
                $query->where('cod_aluno', '<>', $this->studentToIgnore->getKey());
            })
            ->doesntExist();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Já existe uma aluno com este número de inscrição.';
    }
}
