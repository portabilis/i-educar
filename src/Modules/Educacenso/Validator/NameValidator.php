<?php

namespace iEducar\Modules\Educacenso\Validator;

class NameValidator implements EducacensoValidator
{
    private $message;

    private $name;

    public function __construct(string $name)
    {
        $this->name = mb_strtoupper($name);
    }

    public function isValid(): bool
    {
        if ($this->hasFourRepeatedCharaters()) {
            $this->message = 'Nome não pode ter a repetição de 4 caracteres seguidos.';

            return false;
        }

        if (!$this->hasOnlyCharactersAllowed()) {
            $this->message = 'O Nome ou Nome Social não pode conter números ou caracteres especiais como: (0-9!@#\$%^&*?_~-.)';

            return false;
        }

        return true;
    }

    private function hasOnlyCharactersAllowed(): bool
    {
        $pattern = '/^[a-zA-Z\ \'àèìòùÀÈÌÒÙáéíóúýÁÉÍÓÚÝâêîôûÂÊÎÔÛãñõÃÑÕäëïöüÿÄËÏÖÜŸçÇ]+$/';

        return preg_match($pattern, mb_strtoupper($this->name));
    }

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
