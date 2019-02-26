<?php

namespace iEducar\Modules\Educacenso\Validator;

interface EducacensoValidator
{
    public function isValid(): bool;

    public function getMessage();

}
