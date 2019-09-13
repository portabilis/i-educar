<?php

namespace iEducar\Modules\Educacenso\Validator;

class NameValidator implements EducacensoValidator
{
    private $message;

    private $name;

    public function __construct(string $name)
    {
        $this->name = strtoupper($name);
    }

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        if ($this->hasFourRepeatedCharaters()) {
            $this->message = "Nome não pode ter a repetição de 4 caracteres seguidos.";
            return false;
        }

        return true;
    }

    /**
     * @return bool
     */
    private function hasFourRepeatedCharaters(): bool
    {
        return preg_match('/(.)\1\1\1/', $this->name);
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }
}
