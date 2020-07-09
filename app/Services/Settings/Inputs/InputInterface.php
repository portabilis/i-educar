<?php

namespace App\Services\Settings\Inputs;

interface InputInterface
{
    public function getInputView($id, $description, $key, $value);
}
